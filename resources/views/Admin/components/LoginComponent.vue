<template>
  <v-app id="inspire">
    <v-content>
      <v-container
        class="fill-height"
        fluid
      >
        <v-row
          align="center"
          justify="center"
        >
          <v-col
            cols="12"
            sm="8"
            md="4"
          >
            <v-card class="elevation-12">
              <v-toolbar
                color="deep-purple accent-4"
                dark
                flat
              >
                <v-toolbar-title>Login form</v-toolbar-title>
                
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      :href="source"
                      icon
                      large
                      target="_blank"
                      v-on="on"
                    >
                      <v-icon>mdi-account-heart</v-icon>
                    </v-btn>
                    
                    
                  </template>
                  <span>Source</span>
                </v-tooltip>
              </v-toolbar>
              <v-card-text>
               <v-progress-linear
                    :active="loading"
                    :indeterminate="loading"
                    absolute
                    top
                    color="pink"
                ></v-progress-linear>
                <v-form @submit.prevent="login"
                ref="form"
                v-model="valid"
                lazy-validation
              >
                  <v-text-field
                 
                    label="Email"
                    v-model="email"
                    :rules = "emailRules"
                    name="login"
                    prepend-icon="mdi-account"
                    type="text"
                    required
                  ></v-text-field>

                  <v-text-field
            
                    v-model="password"
                    label="Password"
                    name="password"
                    prepend-icon="mdi-lock"
                    required
                    :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                    :rules="[rules.required, rules.min]"
                    :type="show ? 'text' : 'password'"
                    hint="At least 8 characters"
                    counter
                    @click:append="show = !show"
                  ></v-text-field>
              
                <v-btn block dark :disabled="!valid" color="deep-purple accent-4" class="mt-4" type="submit">Login</v-btn>
                </v-form>
              </v-card-text>
            </v-card>
              <v-snackbar
                  v-model="snackbar"
                >
                  {{ text }}
                  <v-btn
                   
                    color="pink"
                    text
                    @click="snackbar = false"
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
    data(){
        return{ 
        show: false,
        valid:true,
        email:'',
        emailRules: [
          v => !!v || "E-mail is required",
          v => /.+@.+\..+/.test(v) || "E-mail must be valid"
        ],
        password:'',
        rules: {
            required: value => !!value || "Required.",
            min: v => v.length >= 8 || "Min 8 characters"
          },
        loading:false,
        snackbar:false,
        text:'',
 
        }
    },
      created () {
      this.$vuetify.theme.dark = true
    },
    methods:{
          login() {
            this.loading=true
            
              this.$store
                  .dispatch("retrieveToken", {
                      email: this.email,
                      password: this.password
                  })
                  .then(response => {
                      this.$router.push('/admin');
                  })
                  .catch(error =>{
                      this.loading=false
                      this.text = error.response.data.message
                      this.snackbar = true
                  })
          
          }

  }
  }
</script>