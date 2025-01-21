<template>
    <SiteTemplate>

        <span slot="principal">
            <h2>Perfil</h2>
            <input type="text" placeholder="Nome" v-model="name">
            <input type="text" placeholder="E-mail" v-model="email">

            <div class="file-field input-field">
                <div class="btn green">
                    <span>Imagem</span>
                    <input type="file" v-on:change="saveImage">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>

            <input type="password" placeholder="Senha" v-model="password">
            <input type="password" placeholder="Confirme sua senha" v-model="password_confirmation">
            <button v-on:click="perfil()" class="btn green">Atualizar perfil</button>
        </span>
        
        <span slot="menu_esquerdo">
            <img :src="userData.image"
                class="responsive-img">
        </span>

    </SiteTemplate>
</template>

<script>
import SiteTemplate from '@/templates/SiteTemplate';

export default {
    name: 'Cadastro',
    data() {
        return {
            userData: false,
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            image: ''
        }
    },
    created() {
        let userAux = this.$store.getters.getUser;
        if (userAux) {
            this.userData = this.$store.getters.getUser; // Transforma em objeto
            this.email = this.userData.email || '';
            this.name = this.userData.name || '';
        }
    },
    components: {
        SiteTemplate
    },
    methods: {
        saveImage(e){
            let file = e.target.files || e.dataTransfer.files;
            
            if(!file.length){
                return;
            }
            
            let reader = new FileReader();
            reader.onloadend = (e) => {
                this.image = e.target.result;
            };
            reader.readAsDataURL(file[0]);
        },
        perfil() {
            //console.log(this.user.email);
            this.$http.put(`${this.$urlAPI}perfil`, {
                name: this.name,
                email: this.email,
                image: this.image,
                password: this.password,
                password_confirmation: this.password_confirmation
            }, {"headers": {"Authorization": "Bearer " + this.$store.getters.getToken}})
                .then(response => {
                    if (response.data.status) {
                        this.userData = response.data.user;
                        this.$store.commit('setUser', response.data.user)
                        sessionStorage.setItem('user', JSON.stringify(this.userData));
                        alert('Perfil atualizado');
                    } 
                    else if(response.data.status == false && response.data.validationErrors){
                        console.log('Erros de validação', response.data);
                        let errors = '';
                        for (let error of Object.values(response.data.errors)) {
                            errors += `${error}\n`;
                        }
                        
                    }
                })
                .catch(e => {
                    console.log(e.response.data);
                    alert('Tente novamente mais tarde!')
                });
        }
    }
}
</script>