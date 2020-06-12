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
                <v-toolbar-title>Register form</v-toolbar-title>

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
                <v-form @submit.prevent="register"
                ref="form"
                v-model="valid"
                lazy-validation
              >
                <v-text-field
                 v-model="name"
                color='error' label="Name"
                :rules="[nameRules.required, nameRules.min]">
                </v-text-field>
                <v-text-field
                    label="Email"
                    v-model="email"
                    prepend-icon="mdi-account"
                    color='error'
                    :rules="[emailRules.required, emailRules.min]">
                </v-text-field>

                  <v-text-field
                    v-model="password"
                    autocomplete="off"
                    label="Password"
                    name="password"
                    prepend-icon="mdi-lock"
                    :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                    :rules="[passwordrules.required, passwordrules.min]"
                    :type="show ? 'text' : 'password'"
                    hint="At least 8 characters"
                    counter
                    @click:append="show = !show"
                  ></v-text-field>
                  <v-text-field
                      :rules="[passwordrules.required,  passwordMatch]"
                      name="input-10-2"
                      label="Retype_Password"
                      value="C_Password"
                      v-model="confirmationpassword"
                      required
                    >
                    </v-text-field>

                <v-btn block dark :disabled="!valid"
                color="deep-purple accent-4" class="mt-4"
                type="submit">Register</v-btn>
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
        success:"",
        error:"",
        text:'',
        valid:true,
        loading:false,
        snackbar:false,
        text:'',
        show: false,
        users: [],
        name:"",
        email:"",
        confirmationpassword:"",
        password:"",
        nameRules: {
            required:v => !!v || "Name is required",
            min:v => (v && v.length <= 10) || "Name must be less than 10 characters"
        },


        emailRules: {
            required:v => !!v || "E-mail is required",
            min:v => /.+@.+\..+/.test(v) || "E-mail must be valid"
        },
        passwordrules: {
            required: value => !!value || "Required.",
            min:v => (v && v.length >= 8) || "Min 8 characters"
            },

            }
    },
    created () {
      this.$vuetify.theme.dark = true
    },

    computed: {

     passwordMatch(){
         if(this.password != this.confirmationpassword){
            return 'Password Does not matched'

            }else {
            return true
            }
        }
    },
    methods: {
        validate() {
        this.$refs.form.validate();
        },

    register() {
        if (this.password !== this.confirmationpassword) {
                    this.errorPasswordAgain = "Passwords do not match.";
                }
                if (this.email) {
                    this.erroremail = "The email has already been taken. please Login .";
                }
                this.loading=true

                this.$store
                    .dispatch("register", {
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        confirmationpassword: this.confirmationpassword
                    })
                    .then(response => {
                        alert("You Have To Varify The Email Sent")
                        this.$router.push('/login');
                        console.log(response.data)
                    })

                .catch(error =>{
                      this.loading=false
                      this.text = error.response.data.message
                      this.snackbar = true
                  })


        }
    },

};
</script>

