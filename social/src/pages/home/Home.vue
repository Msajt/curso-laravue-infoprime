<template>
	<SiteTemplate>

		<span slot="menu_esquerdo">
			<div class="row valign-wrapper">
				<GridVue size=4>
					<router-link :to="'/pagina/' + userData.id + '/' + $slug(userData.name, { lower: true })">
						<img :src="userData.image" :alt="userData.name" class="circle responsive-img">
					</router-link>
					<!-- notice the "circle" class -->
				</GridVue>
				<GridVue size="8">
					<span class="black-text">
						<router-link :to="'/pagina/' + userData.id + '/' + $slug(userData.name, { lower: true })">
							<h5>{{ userData.name }}</h5>
						</router-link>
					</span>
				</GridVue>
			</div>
		</span>

		<span slot="menu_esquerdo_amigos">
			<h3>Seguindo</h3>
			<li v-for="item in friends" :key="item.id">{{ item.name }}</li>
			<li v-if="!friends.length">Nenhum amigo</li>
		</span>

		<span slot="principal">
			<PublishContainer />

			<CardContent v-for="item in listContents" :key="item.id" :id="item.id" :totalLikes="item.totalLikes"
				:userLiked="item.userLiked" :postComments="item.postComments" :userId="item.user.id"
				:avatar="item.user.image" :userName="item.user.name" :postDate="item.date">

				<CardDetail :cardImage="item.image" :cardTitle="item.title" :cardDescription="item.text"
					:cardLink="item.link" />

			</CardContent>
			<button v-if="nextPageUrl" @click="loadPagination()" class="btn blue">Mais...</button>
			<div v-scroll="handleScroll"></div>
		</span>
	</SiteTemplate>
</template>

<script>
import CardContent from '@/components/social/CardContent';
import CardDetail from '@/components/social/CardDetail';
import PublishContainer from '@/components/social/PublishContainer';
import SiteTemplate from '@/templates/SiteTemplate';
import GridVue from '@/components/layout/GridVue'

export default {
	name: 'Home',
	data() {
		return {
			userData: { image: '', name: '' },
			nextPageUrl: null,
			stopPagination: false,
			friends: []
		}
	},
	created() {
		let userAux = this.$store.getters.getUser;
		if (userAux) {
			this.userData = this.$store.getters.getUser; // Transforma em objeto
			// Buscando informações do usuário
			this.$http.get(`${this.$urlAPI}content/list`, { "headers": { "authorization": "Bearer " + this.$store.getters.getToken } })
				.then(response => {
					//console.log(response);
					if (response.data.status) {
						this.$store.commit('setTimelineContents', response.data.contents.data);
						this.nextPageUrl = response.data.contents.next_page_url;

						this.$http.get(`${this.$urlAPI}user/listFriends`, { "headers": { "authorization": "Bearer " + this.$store.getters.getToken } })
							.then(response => {
								if (response.data.status) {
									this.friends = response.data.friends;
								} else alert(response.data.error)
							})
							.catch(e => {
								console.log(e.response.data);
								alert('Tente novamente mais tarde!')
							});
					}
				})
				.catch(e => {
					console.log(e.response.data);
					alert('Tente novamente mais tarde!')
				});
		}
	},
	components: {
		CardContent,
		CardDetail,
		PublishContainer,
		SiteTemplate,
		GridVue
	},
	computed: {
		listContents() {
			//console.log('Teste' + this.$store.getters.getTimelineContents)
			return this.$store.getters.getTimelineContents;
		}
	},
	methods: {
		handleScroll(evt, el) {
			// console.log(window.scrollY);
			// console.log(document.body.clientHeight);
			if (this.stopPagination) {
				return;
			}
			if (window.scrollY >= document.body.clientHeight - 1300) {
				this.stopPagination = true;
				this.loadPagination();
			}
		},
		loadPagination() {
			if (!this.nextPageUrl) return;

			this.$http.get(this.nextPageUrl, { "headers": { "authorization": "Bearer " + this.$store.getters.getToken } })
				.then(response => {
					//console.log(response);
					if (response.data.status && this.$route.name == "Home") {
						this.$store.commit('setPaginationTimelineContents', response.data.contents.data);
						this.nextPageUrl = response.data.contents.next_page_url;
						this.stopPagination = false;
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