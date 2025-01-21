<template>
    <span>
        <!-- Imagem -->
        <div v-if="cardImage && cardImage != '#'" class="card-image">
            <a v-if="cardLink && cardLink != '#'" :href="cardLink" target="_blank">
                <img :src="cardImage">
                <span class="card-title">{{ cardTitle || '' }}</span>
            </a>
            <span v-if="!cardLink || cardLink == '#'">
                <img :src="cardImage">
                <span class="card-title">{{ cardTitle || '' }}</span>
            </span>
        </div>

        <!-- Descrição da imagem -->
        <div class="card-content">
            <span v-if="!cardImage || cardImage == '#'">
                <span class="card-title">{{ cardTitle || '' }}</span>
            </span>
            <p>{{ cardDescription || '' }}</p>
            <p v-if="!cardLink || cardLink != '#'"><a :href="cardLink" target="_blank">{{ cardLink }}</a></p>
        </div>
    </span>
</template>

<script>

export default {
    name: 'CardDetail',
    components: {

    },
    props: ['cardTitle', 'cardImage', 'cardDescription', 'cardLink']
}
</script>