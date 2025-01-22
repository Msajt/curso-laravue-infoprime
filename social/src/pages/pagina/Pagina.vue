<template>
	<SiteTemplate>

		<span slot="menu_esquerdo">
			<div class="row valign-wrapper">
				<GridVue size=4>
					<router-link :to="'/pagina/' + pageOwner.id + '/' + $slug(pageOwner.name, { lower: true })">
						<img :src="pageOwner.image" :alt="pageOwner.name" class="circle responsive-img">
					</router-link>
					<!-- notice the "circle" class -->
				</GridVue>
				<GridVue size="8">
					<span class="black-text">
						<router-link :to="'/pagina/' + pageOwner.id + '/' + $slug(pageOwner.name, { lower: true })">
							<h5>{{ pageOwner.name }}</h5>
						</router-link>
						<button v-if="showFriendButton" @click="addFriend(pageOwner.id)" class="btn">{{ textButton
							}}</button>
					</span>
				</GridVue>
			</div>
		</span>

		<span slot="menu_esquerdo_amigos">
			<h3>Seguindo</h3>
			<router-link v-for="item in friends" :key="item.id"
				:to="'/pagina/' + item.id + '/' + $slug(item.name, { lower: true })">
				<li>{{ item.name }}</li>
			</router-link>
			<li v-if="!friends.length">Nenhum amigo</li>

			<h3>Seguidores</h3>
			<router-link v-for="item in followers" :key="item.id" :to="'/pagina/' + item.id + '/' + $slug(item.name, { lower: true })">
				<li >{{ item.name }}</li>
			</router-link>
			<li v-if="!followers.length">Nenhum seguidor</li>
		</span>

		<span slot="principal">
			<PublishContainer />

			<CardContent v-for="item in listContents" :key="item.id" :id="item.id" :totalLikes="item.totalLikes"
				:userLiked="item.userLiked" :postComments="item.postComments" :avatar="item.user.image"
				:userId="item.user.id" :userName="item.user.name" :postDate="item.date">

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
	name: 'Pagina',
	data() {
		return {
			userData: false,
			nextPageUrl: null,
			stopPagination: false,
			pageOwner: {
				image: '',
				name: ''
			},
			showFriendButton: false,
			friends: [],
			loggedFriends: [],
			textButton: 'Seguir',
			followers: []
		}
	},
	created() {
		this.refreshPage();
	},
	components: {
		CardContent,
		CardDetail,
		PublishContainer,
		SiteTemplate,
		GridVue
	},
	watch: {
		'$route': 'refreshPage'
	},
	computed: {
		listContents() {
			//console.log('Teste' + this.$store.getters.getTimelineContents)
			return this.$store.getters.getTimelineContents;
		}
	},
	methods: {
		refreshPage() {
			let userAux = this.$store.getters.getUser;
			if (userAux) {
				this.userData = this.$store.getters.getUser; // Transforma em objeto
				// Buscando informações do usuário
				this.$http.get(`${this.$urlAPI}content/page/list/` + this.$route.params.id, { "headers": { "authorization": "Bearer " + this.$store.getters.getToken } })
					.then(response => {
						//console.log(response);
						if (response.data.status && this.$route.name == "Pagina") {
							this.$store.commit('setTimelineContents', response.data.contents.data);
							this.nextPageUrl = response.data.contents.next_page_url;
							this.pageOwner = response.data.owner;

							if (this.pageOwner.id != this.userData.id) this.showFriendButton = true;
							else this.showFriendButton = false;

							this.$http.get(`${this.$urlAPI}user/listFriendsPage/${this.pageOwner.id}`, { "headers": { "authorization": "Bearer " + this.$store.getters.getToken } })
								.then(response => {
									if (response.data.status) {
										this.friends = response.data.friends;
										this.loggedFriends = response.data.loggedFriends;
										isFriend();
										this.followers = response.data.followers;
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
		isFriend() {

			for (let friend of this.loggedFriends) {
				if (friend.id == this.pageOwner.id) {
					this.textButton = 'Remover';
					return
				}
				this.textButton = 'Seguir'
			}
		},
		addFriend(id) {
			this.$http.post(this.$urlAPI + `user/friend`, { id },
				{ "headers": { "authorization": "Bearer " + this.$store.getters.getToken } })
				.then(response => {
					if (response.data.status) {
						console.log(response);
						this.loggedFriends = response.data.friends;
						this.followers = response.data.followers;
						this.isFriend();
					} else {
						alert(response.data.erro);
					}
				}).catch(e => {
					console.log(e)
					alert("Erro! Tente novamente mais tarde!");
				});
		},
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
					if (response.data.status && this.this.$route.name == "Pagina") {
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