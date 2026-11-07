<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    meta: { type: Object, required: true },
});
</script>

<!--
    Keeps the server-printed tags (resources/views/app.blade.php) in step during client-side
    navigation: each head-key replaces the server tag with the matching `inertia` attribute.

    JSON-LD is deliberately server-only. Inertia's <Head> renders v-html as an attribute, not as
    script content, so managing it here would replace the structured data with an empty script.
    Crawlers load every URL directly, so the server copy is always the right one.
-->
<template>
    <Head>
        <title>{{ meta.title }}</title>
        <meta head-key="description" name="description" :content="meta.description" />
        <link head-key="canonical" rel="canonical" :href="meta.url" />

        <meta head-key="og:type" property="og:type" :content="meta.type" />
        <meta head-key="og:url" property="og:url" :content="meta.url" />
        <meta head-key="og:title" property="og:title" :content="meta.title" />
        <meta head-key="og:description" property="og:description" :content="meta.description" />
        <meta head-key="og:image" property="og:image" :content="meta.image" />
        <meta head-key="og:image:alt" property="og:image:alt" :content="meta.imageAlt" />

        <meta head-key="twitter:title" name="twitter:title" :content="meta.title" />
        <meta head-key="twitter:description" name="twitter:description" :content="meta.description" />
        <meta head-key="twitter:image" name="twitter:image" :content="meta.image" />
        <meta head-key="twitter:image:alt" name="twitter:image:alt" :content="meta.imageAlt" />
    </Head>
</template>
