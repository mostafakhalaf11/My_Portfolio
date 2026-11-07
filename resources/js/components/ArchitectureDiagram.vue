<script setup>
import { computed, useId } from 'vue';

// Unique per instance, so two diagrams on one page never share title, description or marker ids.
const id = useId();

const props = defineProps({
    diagram: { type: Object, default: () => ({ nodes: [], edges: [] }) },
    height: { type: Number, default: 420 },
});

const GROUP_COLORS = {
    edge: 'var(--color-ink-muted)',
    tenancy: 'var(--color-signal)',
    authz: 'var(--color-signal)',
    domain: 'var(--color-ink)',
    api: 'var(--color-signal)',
    boundary: 'var(--color-ink-muted)',
    async: 'var(--color-ink-muted)',
};

const WIDTH = 900;
const NODE_W = 178;
const NODE_H = 46;

// Nodes are laid out in columns by declaration order: a dependency-ish flow,
// computed rather than hand-positioned so the CMS stays the source of truth.
const layout = computed(() => {
    const nodes = props.diagram?.nodes ?? [];
    const count = nodes.length;
    if (!count) {
        return { nodes: [], edges: [], relationships: [], unconnectedNodes: [], description: '', height: props.height };
    }

    const perColumn = 3;
    const columns = Math.ceil(count / perColumn);
    const colGap = (WIDTH - NODE_W) / Math.max(columns - 1, 1);

    const positioned = nodes.map((node, i) => {
        const col = Math.floor(i / perColumn);
        const row = i % perColumn;
        const rowsInCol = Math.min(perColumn, count - col * perColumn);
        const rowGap = 108;
        const colHeight = (rowsInCol - 1) * rowGap;

        return {
            ...node,
            x: col * colGap,
            y: (props.height - NODE_H) / 2 - colHeight / 2 + row * rowGap,
            color: GROUP_COLORS[node.group] ?? 'var(--color-ink-muted)',
        };
    });

    const byId = Object.fromEntries(positioned.map((n) => [n.id, n]));

    const edges = (props.diagram?.edges ?? [])
        .map((edge) => {
            const from = byId[edge.from];
            const to = byId[edge.to];
            if (!from || !to) return null;

            const x1 = from.x + NODE_W;
            const y1 = from.y + NODE_H / 2;
            const x2 = to.x;
            const y2 = to.y + NODE_H / 2;
            const mid = x1 + (x2 - x1) / 2;

            return {
                ...edge,
                fromLabel: from.label,
                toLabel: to.label,
                path: `M ${x1} ${y1} C ${mid} ${y1}, ${mid} ${y2}, ${x2} ${y2}`,
                labelX: mid,
                labelY: (y1 + y2) / 2 - 6,
            };
        })
        .filter(Boolean);

    const relationships = edges.map(({ fromLabel, toLabel, label }) => ({ fromLabel, toLabel, label }));
    const connectedNodeIds = new Set(edges.flatMap((edge) => [edge.from, edge.to]));
    const unconnectedNodes = positioned.filter((node) => !connectedNodeIds.has(node.id));
    const description = [
        relationships.length
            ? `Relationships: ${relationships
                  .map(({ fromLabel, toLabel, label }) => `${fromLabel} to ${toLabel}${label ? ` (${label})` : ''}`)
                  .join('; ')}.`
            : '',
        unconnectedNodes.length ? `Components: ${unconnectedNodes.map((node) => node.label).join(', ')}.` : '',
    ]
        .filter(Boolean)
        .join(' ');

    return { nodes: positioned, edges, relationships, unconnectedNodes, description, height: props.height };
});
</script>

<template>
    <figure v-if="layout.nodes.length" class="w-full">
        <ol v-if="layout.relationships.length || layout.unconnectedNodes.length" class="space-y-3 md:hidden" aria-label="Architecture relationships">
            <li
                v-for="(relationship, index) in layout.relationships"
                :key="`mobile-edge-${index}`"
                class="rounded-lg border border-[color:var(--color-wire)] p-3"
            >
                <p class="text-sm font-medium">
                    {{ relationship.fromLabel }} <span aria-hidden="true">→</span> {{ relationship.toLabel }}
                </p>
                <p v-if="relationship.label" class="mt-1 text-xs text-[color:var(--color-ink-muted)]">{{ relationship.label }}</p>
            </li>
            <li
                v-for="node in layout.unconnectedNodes"
                :key="`mobile-node-${node.id}`"
                class="rounded-lg border border-[color:var(--color-wire)] p-3 text-sm font-medium"
            >
                {{ node.label }}
            </li>
        </ol>

        <svg
            :viewBox="`-8 0 ${WIDTH + 16} ${layout.height}`"
            class="hidden h-auto w-full md:block"
            role="img"
            :aria-labelledby="`${id}-title`"
            :aria-describedby="`${id}-description`"
        >
            <title :id="`${id}-title`">System architecture diagram</title>
            <desc :id="`${id}-description`">{{ layout.description }}</desc>
            <defs>
                <marker :id="`${id}-arrow`" markerWidth="8" markerHeight="8" refX="7" refY="4" orient="auto">
                    <path d="M0,1 L7,4 L0,7" fill="none" stroke="var(--color-diagram-wire)" stroke-width="1.4" />
                </marker>
            </defs>

            <g>
                <path
                    v-for="(edge, i) in layout.edges"
                    :key="`e${i}`"
                    :d="edge.path"
                    fill="none"
                    stroke="var(--color-diagram-wire)"
                    stroke-width="1.6"
                    :marker-end="`url(#${id}-arrow)`"
                />
                <text
                    v-for="(edge, i) in layout.edges"
                    :key="`l${i}`"
                    :x="edge.labelX"
                    :y="edge.labelY"
                    text-anchor="middle"
                    class="fill-[color:var(--color-ink-muted)] font-mono"
                    style="font-size: 10px"
                >
                    {{ edge.label }}
                </text>
            </g>

            <g v-for="node in layout.nodes" :key="node.id">
                <rect
                    :x="node.x"
                    :y="node.y"
                    :width="NODE_W"
                    :height="NODE_H"
                    rx="8"
                    fill="var(--color-surface-raised)"
                    :stroke="node.color"
                    stroke-width="1.5"
                />
                <text
                    :x="node.x + NODE_W / 2"
                    :y="node.y + NODE_H / 2 + 4"
                    text-anchor="middle"
                    class="fill-[color:var(--color-ink)]"
                    style="font-size: 12px; font-weight: 500"
                >
                    {{ node.label }}
                </text>
            </g>
        </svg>
    </figure>
</template>
