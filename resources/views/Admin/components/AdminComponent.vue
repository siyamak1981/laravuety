<template>
  <v-app id="inspire">
    <v-navigation-drawer
      v-model="drawer"
      app
      clipped
    >
      <v-list dense>
        <v-list-item
          v-for="item in items"
          :key="item.text"
          link
          :to="item.action"
        >
          <v-list-item-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>
              {{ item.text }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-subheader class="mt-4 grey--text text--darken-1">SUBSCRIPTIONS</v-subheader>
        <v-list>
          <v-list-item
            v-for="item in items2"
            :key="item.text"
            link
          >
            <v-list-item-avatar>
              <img
                :src="`https://randomuser.me/api/portraits/men/${item.picture}.jpg`"
                alt=""
              >
            </v-list-item-avatar>
            <v-list-item-title v-text="item.text"></v-list-item-title>
          </v-list-item>
        </v-list>
        <v-list-item
          class="mt-4"
          link
        >

          <v-list-item-title class="grey--text text--darken-1">
           <v-switch class="ma-4" v-model ="theme" label="Theme">
           </v-switch>
           </v-list-item-title>

        </v-list-item>
        <v-list-item link @click="logout">
          <v-list-item-action>
            <v-icon color="grey darken-1">mdi-logout</v-icon>
          </v-list-item-action>
          <v-list-item-title class="grey--text text--darken-1">Logout</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar
      app
      clipped-left
      color="deep-purple accent-4"

    >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

      <v-toolbar-title class="mr-12 align-center">
        <span class="title">Dashboard</span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>mdi-apps</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-bell</v-icon>
      </v-btn>
      <v-btn
        icon
        large
      >
        <v-avatar
          size="32px"
          item
        >
          <v-img
            src="https://cdn.vuetifyjs.com/images/logos/logo.svg"
            alt="Vuetify"
          ></v-img>
        </v-avatar>
      </v-btn>
    </v-row>
  </v-app-bar>
  <v-col>
<router-view></router-view>
  </v-col>
  <v-content>
    <v-container class="fill-height">
      <v-row
        justify="center"
        align="center"
      >
        <v-col>
          <v-snackbar
                v-model="snackbar"
                top
                right
                color="pink"
            >
                You Are LoggedIn Sueccessfully
                <v-btn
                  text
                  @click="snackbar"
                >
                Close
              </v-btn>
            </v-snackbar>
          </v-col>
        </v-row>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  export default {
    props: {
      source: String,
    },
    data: () => ({
      theme:true,
      drawer: null,
      snackbar:false,
      items: [
        { icon: 'mdi-trending-up', text: 'Post', action:'#' },
        { icon: 'mdi-youtube-subscription', text: 'Category', action:'#' },
        { icon: 'mdi-history', text: 'Tag', action:'#' },
        { icon: 'mdi-playlist-play', text: 'Roles', action:"/admin/roles" },
        { icon: 'mdi-account-heart', text: 'Users', action:'/admin/users' },
        { icon: 'mdi-castle', text: 'Permissions', action:'/admin/permissions' },
      ],
      items2: [
        // { picture: 28, text: 'Joseph' },
        // { picture: 38, text: 'Apple' },
        // { picture: 48, text: 'Xbox Ahoy' },
        // { picture: 58, text: 'Nokia' },
        // { picture: 78, text: 'MKBHD' },
      ],
    }),
    created () {
      this.$vuetify.theme.dark = true
      this.snackbar = true
    },
    watch:{
      theme(old, newval){
        this.$vuetify.theme.dark = old
      }
    },
    methods:{
      logout(){
         this.$store.dispatch("destroyToken")
            .then(response =>{
            this.$router.push("/login")
                this.snackbar = true;
                this.text = "You are LoggedOut Successfully";

            })
            .catch(error =>{
              console.log(error)
            })
    }
  }
  }
</script>
