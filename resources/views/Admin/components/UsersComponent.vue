
<template>
  <v-data-table
    color="error"
    item-key="name" 
    class="elevation-1" 
    :loading="loading"
     loading-text="Loading... Please wait"
    :headers="headers"
    :items="users.data"
    :server-items-length = "users.total"
    @pagination = "paginate"
    :items-per-page=5
    sort-by="calories"
    show-select
    @input="selectAll"
    :footer-props="{
          itemsPerPageOptions:[5, 10, 15],
          itemsPerPageText:'User Per Page',
          'show-current-page':true,
          'show-first-last-page':true
      }"
  >
    <template v-slot:top>
      <v-toolbar flat color="dark">
        <v-toolbar-title>My User</v-toolbar-title>
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
          <v-text-field 
            @input="serachIt"
              class="mt-8 ml-8" 
              append-icon="mdi-magnify"
              label = "Search..." />
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" max-width="500px">
          <template v-slot:activator="{ on }">
            <v-btn color="deep-purple accent-4" dark class="mr-4" v-on="on">New User</v-btn>
            <v-btn color="pink" dark class="mr-4"  @click="deleteAll"><v-icon>mdi-delete </v-icon> Delete</v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>
           <v-form 
                @submit.stop.prevent="save"
                ref="form"
                v-model="valid"
                lazy-validation
              >
  
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12" sm="12" md="12">
                    <v-text-field v-model="editedItem.name" color='error' label="Name" :rules="[nameRules.required, nameRules.min]"></v-text-field>
                  </v-col>
                  </v-row>
                  <v-row v-if="editedIndex == -1">
                  <v-col cols="12" sm="12" md="12">
                    <v-text-field v-model="editedItem.email" color='error'
                     autocomplete="off" :success-messages="success"
                      :error-messages="error" label="Email" :blur="checkEmail" 
                      :rules="[emailRules.required, emailRules.min]"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12" md="12">
                  <v-text-field
                    v-model="editedItem.password"
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
                  </v-col> 
                  <v-col cols="12" sm="12" md="12">
                  <v-text-field
                      :rules="[passwordrules.required,  passwordMatch]"
                      name="input-10-2"
                      label="Retype_Password"
                      value="C_Password"
                      v-model="editedItem.confirmationpassword"
                    >
                    </v-text-field>
                  </v-col>
                  </v-row>
                  <v-row>
                  <v-col cols="12" sm="12" md="12">
                    <v-select
                      :items ="roles"
                      v-model="editedItem.role"
                      label="Role"
                      
                    ></v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
              <v-btn type="submit" :disabled="!valid" color="blue darken-1" text @click.prevent="save">Save</v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>
    </v-toolbar>
  </template>
     <template v-slot:item.photo="{ item }">
      <v-img
          :src ="item.photo"
          :lazy-src ="item.photo"
          aspect-ratio="1"
          class="grey lighten-2"
          max-width="100"
          max-height="100"
        
        >
      </v-img>
  </template>
    <template v-slot:item.actions="{ item }">
      <v-icon
        small
        class="mr-2"
        @click="editItem(item)"

      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>
 
    <template v-slot:no-data>
      <v-btn color="deep-purple accent-4" @click="initialize">Reset</v-btn>
    </template>
     <v-snackbar
        v-model="snackbar"
      >
    <div>{{ text }}</div>
        <v-btn
          color="pink"
          text
          @click="snackbar = false"
        >
        Close
      </v-btn>
    </v-snackbar>
  </v-data-table>
</template>

<script>
  export default {
    
    data: () => ({
      success:"",
      error:"",
      valid:true,
      snackbar : false,
      show: false,
      dialog: false,
      loading:false,
      selected :[],
      text:'',
      users: [],
      roles:[],
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

      headers: [
        {
          text: 'id',
          align: 'start',
          sortable: false,
          value: 'id',
        },


        { text: 'Name', value: 'name' },
        { text: 'Email', value: 'email' },
        { text: 'Role', value: 'role' },
        { text: 'Photo', value: 'photo' },
        { text: 'Created', value: 'created_at' },
        { text: 'Updated', value: 'updated_at' },
        { text: 'Actions', value: 'actions', sortable: false },
      ],

      editedIndex: -1,
      editedItem: {
        id:"",
        name: "",
        email:"",
        password:"",
        confirmationpassword:"",
        role:"",

      },
      defaultItem: {
        id:"",
        name: "",
        email:"",
        role:"",
        photo:"",

      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },
     passwordMatch(){

         if(this.editedItem.password != this.editedItem.confirmationpassword){
            return 'Password Does not matched'
           
        }else {
          return true
        }
     },
     checkEmail(){
       if(/.+@.+\..+/.test(this.editedItem.email)){
         axios.post('/auth/email/validate', {
           'email':this.editedItem.email
         })
         .then(response =>{
           this.success = response.data.message;
           this.error = "";
         })
         .catch(error =>{
           this.success = "";
           this.error = "Email Already Exists";
         })
       }
     }
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
    },

    created () {
      this.initialize()
    },

  methods: {
    selectAll(ev){
          this.selected = [];
          if(ev.length > 0){
            this.selected = ev.map(val => val.id)
          }
          console.dir(this.selected)
    },
    deleteAll(){
       
        let decided =  confirm('Are you sure you want to delete these item?')
        if(decided){
        this.loading=true
        axios.post('auth/users/delete', { 'users':this.selected })
        .then(response =>{
          this.text = "Record Deleted Successfully";
          this.selected.map(val =>{
            const index = this.users.data.indexOf(val)
            this.users.data.splice(index)
            this.snackbar = true
            this.loading=false
          })
        })
        .catch(error =>{
          console.log(error)
          this.text ="Error Deleting Record"
          this.snackbar = true
        }
        )
       
        }
    },
    serachIt(val){
     if(val.length > 2){
        axios.get(`/auth/users/${val}`)
        .then(response => this.users = response.data.users)
        .catch(error =>console.log(error))  
      }
         
       if(val.length <= 0 ){
        axios.get(`/auth/users/${val}`)
        .then(response => this.users = response.data.users)
        .catch(error =>console.log(error)) 
      }
          
    },
   
    paginate(event){

      axios.get(`/auth/users?page=${event.page}`, {params:{'per_page':event.itemsPerPage}})
        .then(response =>{
          console.dir(event)
          this.users = response.data.users
            this.roles = response.data.roles
      })
      .catch(error =>{
          if(error.response.status == 401){
              localStorage.removeItem('access_token')
              window.location.href="/login"
          
          }
      })
      },
        
    initialize () {
        this.users = []
      },

      editItem (item) {
        this.editedIndex = this.users.data.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.users.data.indexOf(item)
        let decided =  confirm('Are you sure you want to delete this item?')
        if(decided){
        this.loading=true
        axios.delete(`/auth/users/${item.id}`)
        .then(response =>{
          this.text = "Record Deleted Successfully";
          this.snackbar = true
          this.users.data.splice(index, 1)
          this.loading=false
        })
        .catch(error =>
          console.log(error)
        )
       
        }
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },

      save () {
        if (this.editedIndex > -1) {
          const index = this.editedIndex
         this.loading=true
          axios.put(`/auth/users/${this.editedItem.id}`,  this.editedItem )
         .then(response=>{
           this.text = "Record Updated Successfully";
           this.snackbar = true;
          Object.assign(this.users.data[index], response.data.user, this.loading=false)

         })
        
         .catch(error =>{
           console.log(error)
            this.text = "Error Updating Record";
            this.snackbar = true;
         })
        } else {
              this.loading=true
              axios.post('/auth/users', this.editedItem)
            .then(response =>{
                this.loading=false
                this.text ="Record Added Successfully"
                this.snackbar = true;
                this.users.data.push(response.data.user)
                // this.close()
            })
            .catch(error => {
                console.log(error)
                 this.text = "Error Inserting Record"
                 this.snackbar = true
            })
        }
        this.close()
      }
    }
  }
</script>


