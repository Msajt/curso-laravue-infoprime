<template>
    <LoginTemplate>

        <span slot="principal">
            <h2>Login</h2>
            <input type="text" placeholder="E-mail" v-model="user.email">
            <input type="password" placeholder="Senha" v-model="user.password">
            <button class="btn green" v-on:click="login()">Entrar</button>
            <router-link to="/cadastro" type="button" class="btn green darken-2">Cadastro</router-link>
        </span>
        <span slot="menu_esquerdo">
            <img src="https://suaimprensa.com.br/wp-content/uploads/2024/04/Benchmarking-de-social-media.png"
                class="responsive-img">
        </span>

    </LoginTemplate>
</template>

<script>
import axios from 'axios';
import LoginTemplate from '@/templates/LoginTemplate.vue';

export default {
    name: 'Login',
    data: function () {
        return {
            user: {
                email: '',
                password: ''
            }
        };
    },
    methods: {
        login() {
            //console.log(this.user.email);
            this.$http.post(`${this.$urlAPI}login`, {
                email: this.user.email,
                password: this.user.password
            })
                .then(response => {
                    //console.log(response);
                    if (response.data.status) {
                        console.log('login sucesso');
                        this.$store.commit('setUser', response.data.user)
                        sessionStorage.setItem('user', JSON.stringify(response.data.user));
                        this.$router.push('/'); // Variável global do vue
                    } // login sucesso
                    else if (response.data.status == false && response.data.validation) {
                        console.log('erros de verificação');
                        let errors = '';
                        for (let error of Object.values(response.data.validationErrors)) {
                            errors += `${error} `
                        }
                        alert(errors);
                    } // login não existe
                    else {
                        console.log('login não existe');
                        alert('Login inválido')
                    }
                })
                .catch(e => {
                    console.log(e);
                    alert('Tente novamente mais tarde!')
                });
        }
    },
    components: {
        LoginTemplate
    }
}
</script>