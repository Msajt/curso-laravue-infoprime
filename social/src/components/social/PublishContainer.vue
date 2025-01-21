<template>
    <div class="row">
        <GridVue class="input-field" size="12">
            <input type="text" v-model="content.title">
            <textarea v-if="content.title" placeholder="Conteúdo" v-model="content.text"
                class="materialize-textarea"></textarea>
            <input v-if="content.title && content.text" type="text" placeholder="Link" v-model="content.link">
            <input v-if="content.title && content.text" type="text" placeholder="Imagem" v-model="content.image">
            <label>O que está acontecendo</label>
        </GridVue>
        <p class="right-align">
            <button @click="addContent()" v-if="content.title && content.text"
                class="btn green waves-effect waves-light col s" size="2 offset-s10">
                Publish
            </button>
        </p>
    </div>
</template>

<script>
import GridVue from '@/components/layout/GridVue';
export default {
    name: 'PublishContainer',
    props: [],
    components: {
        GridVue,
    },
    props: [],
    data() {
        return {
            content: {
                title: '',
                text: '',
                link: '',
                image: ''
            }
        }
    },
    methods: {
        addContent() {
            console.log(this.content);
            this.$http.post(`${this.$urlAPI}content/add`,
                this.content, {
                "headers": { "authorization": "Bearer " + this.$store.getters.getToken }
            })
                .then(response => {
                    if (response.data.status) {
                        console.log(response.data.contents);
                        this.content = {
                            title: '',
                            text: '',
                            link: '',
                            image: ''
                        };
                        this.$store.commit('setTimelineContents', response.data.contents.data);
                    } else if (response.data.status == false && response.data.validationErrors) {
                        // Erros de validação
                        let errors = '';
                        for (let error of Object.values(response.data.errors)) {
                            errors += `${error}\n`;
                        }

                    }
                })
                .catch(e => {
                    console.log(e);
                    alert("Erro! Tente mais tarde!");
                })
        }
    }
}
</script>