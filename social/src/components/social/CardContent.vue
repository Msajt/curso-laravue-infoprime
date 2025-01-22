<template>
    <div class="row">
        <div class="card">
            <div class="card-content">
                <!-- Dados do usuário -->
                <div class="row valign-wrapper">
                    <GridVue size=1>
                        <router-link :to="'/pagina/' + userId + '/' + $slug(userName, {lower:true})">
                            <img :src="avatar" :alt=userName class="circle responsive-img">
                        </router-link>
                        <!-- notice the "circle" class -->
                    </GridVue>
                    <GridVue size="11">
                        <span class="black-text">
                            <strong><router-link :to="'/pagina/' + userId + '/' + $slug(userName, {lower:true})">{{ userName }}</router-link></strong> - <small>{{ postDate }}</small>
                        </span>
                    </GridVue>
                </div>

                <slot></slot>

            </div>
            <div class="card-action">
                <p>
                    <a style="cursor: pointer" @click="like(id)">
                        <i class="material-icons">{{ liked }}</i>{{ likesTotal }}
                    </a>
                    <a style="cursor: pointer" @click="openComments()">
                        <i class="material-icons">insert_comment</i>{{ listComments.length }}
                    </a>

                </p>
                <p v-if="showComments" class="right-align">
                    <input type="text" placeholder="Comentar" v-model="commentText">
                    <button @click="comment(id)" v-if="commentText" class="btn waves-effect waves-light orange"><i
                            class="material-icons">send</i></button>
                </p>
                <p v-if="showComments">
                <ul class="collection">
                    <li class="collection-item avatar" v-for="item in postComments" :key="item.id">
                        <img :src="item.user.image" alt="" class="circle">
                        <span class="title">{{ item.user.name }}<small>- {{ item.date }}</small></span>
                        <p>{{ item.text }}</p>
                    </li>
                </ul>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import GridVue from '@/components/layout/GridVue';
console.log(this.postComments)
export default {
    name: 'CardContent',
    components: {
        GridVue,
    },
    props: ['id', 'avatar', 'userName', 'postDate', 'totalLikes', 'userLiked', 'postComments', 'userId'],
    data() {
        return {
            liked: this.userLiked ? 'favorite' : 'favorite_border',
            likesTotal: this.totalLikes,
            showComments: false,
            commentText: '',
            listComments: this.postComments || []
        }
    },
    methods: {
        like(id) {
            let url = '';
            if(this.$route.name == "Home") url = 'content/like/'
            else url = 'content/likePage/'
            
            this.$http.put(this.$urlAPI + url + id, {},
                { "headers": { "authorization": "Bearer " + this.$store.getters.getToken } }
            )
                .then(response => {
                    if (response.status) {
                        this.likesTotal = response.data.likes;
                        this.$store.commit('setTimelineContents', response.data.list.contents.data);
                        if (this.liked == 'favorite_border') this.liked = 'favorite';
                        else this.liked = 'favorite_border';
                    } else {
                        alert(response.data.error)
                    }

                })
                .catch(e => {
                    console.log(e);
                    alert('Tente novamente mais tarde!')
                });
        },
        openComments() {
            this.showComments = !this.showComments;
        },
        comment(id) {
            if (!this.commentText) return;

            let url = '';
            if(this.$route.name == "Home") url = 'content/comment/'
            else url = 'content/commentPage/'

            this.$http.put(this.$urlAPI + url + id, { text: this.commentText },
                { "headers": { "authorization": "Bearer " + this.$store.getters.getToken } }
            )
                .then(response => {
                    if (response.data.status) {
                        this.$store.commit('setTimelineContents', response.data.list.contents.data);
                        this.commentText = ''
                    } else alert(response.data.error)
                })
                .catch(e => {
                    console.log(e);
                    alert('Tente novamente mais tarde!')
                });
        }

    }
}
</script>