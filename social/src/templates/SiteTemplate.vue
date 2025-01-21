<template>
  <span>
    <header>
      <NavbarVue color="green darken-1" logo="Social" url="/">
        <li><router-link to="/">Home</router-link></li>
        <li v-if="!user"><router-link to="/login">Login</router-link></li>
        <li v-if="!user"><router-link to="/cadastro">Cadastre-se</router-link></li>
        <li v-if="user"><router-link to="/perfil">{{ user.name }}</router-link></li>
        <li v-if="user"><a v-on:click="logout()">Sair</a></li>
      </NavbarVue>
    </header>

    <main>
      <div class="container">
        <div class="row">
          <GridVue size="4">
            <CardMenu>
              <slot name="menu_esquerdo"></slot>
            </CardMenu>
            <CardMenu>
              <h3>Amigos</h3>
              <li>Amigo 1</li>
              <li>Amigo 1</li>
              <li>Amigo 1</li>
              <li>Amigo 1</li>
            </CardMenu>
          </GridVue>
          <GridVue size="8">
            <slot name="principal"></slot>
          </GridVue>
        </div>
      </div>
    </main>

    <FooterVue color="green darken-1" logo="Social" description="Teste de descrição" year="2025">
      <li><a class="grey-text text-lighten-3" href="#!">Home</a></li>
      <li><a class="grey-text text-lighten-3" href="#!">Link 2</a></li>
      <li><a class="grey-text text-lighten-3" href="#!">Link 3</a></li>
      <li><a class="grey-text text-lighten-3" href="#!">Link 4</a></li>
    </FooterVue>
  </span>
</template>

<script>
import NavbarVue from '@/components/layout/NavbarVue';
import FooterVue from '@/components/layout/FooterVue';
import GridVue from '@/components/layout/GridVue';
import CardMenu from '@/components/layout/CardMenu';

export default {
  name: 'SiteTemplate',
  data(){
    return{
      user: false
    }
  },
  methods: {
    logout(){
      this.$store.commit('setUser', null)
      sessionStorage.clear();
      this.user = false;
      this.$router.push('/login');
    }
  },
  components: {
    NavbarVue,
    FooterVue,
    GridVue,
    CardMenu
  },
  created(){
    let userAux = this.$store.getters.getUser;
    if(userAux){
      this.user = this.$store.getters.getUser; // Transforma em objeto
    } else {
      this.$router.push('/login');
    }
  }
}
</script>

<style></style>