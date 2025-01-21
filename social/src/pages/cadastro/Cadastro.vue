<template>
    <LoginTemplate>

        <span slot="principal">
            <h2>Cadastro</h2>
            <input type="text" placeholder="Nome" v-model="user.name">
            <input type="text" placeholder="E-mail" v-model="user.email">
            <input type="password" placeholder="Senha" v-model="user.password">
            <input type="password" placeholder="Confirme sua senha" v-model="user.password_confirmation">
            <button v-on:click="cadastro()" class="btn green">Enviar</button>
            <router-link to="/login" type="button" class="btn green darken-2">Já possuo conta</router-link>
        </span>
        <span slot="menu_esquerdo">
            <img src="https://suaimprensa.com.br/wp-content/uploads/2024/04/Benchmarking-de-social-media.png"
                class="responsive-img">
        </span>

    </LoginTemplate>
</template>

<script>
import LoginTemplate from '@/templates/LoginTemplate.vue';
import axios from 'axios';

export default {
    name: 'Cadastro',
    data() {
        return {
            user: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    components: {
        LoginTemplate
    },
    methods: {
        cadastro() {
            //console.log(this.user.email);
            this.$http.post(`${this.$urlAPI}cadastro`, {
                name: this.user.name,
                email: this.user.email,
                password: this.user.password,
                password_confirmation: this.user.password_confirmation
            })
                .then(response => {
                    console.log(response, this.user);
                    if (response.data.status) {
                        console.log('cadastro realizado com sucesso');
                        this.$store.commit('setUser', response.data.user)
                        sessionStorage.setItem('user', JSON.stringify(response.data.user));
                        this.$router.push('/'); // Variável global do vue
                    } // login sucesso
                    else if (response.data.status == false && response.data.validationErrors) {
                        console.log('Erros de validação');
                        let errors = '';
                        for (let error of Object.values(response.data.validationErrors)){
                            errors += `${error} `
                        }
                        alert(errors);
                        
                    }// login não existe
                    else {
                        console.log('Erro ao cadastrar'); 
                        alert('Erro ao cadastrar')
                    }
                })
                .catch(e => {
                    console.log(e);
                    alert('Tente novamente mais tarde!')
                });
        }
    }
}
</script>