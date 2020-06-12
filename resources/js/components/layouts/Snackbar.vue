<template>
	<v-snackbar
    :value="snackbar"
    bottom
    :timeout="snackbarTimeout">
    {{ snackbarText }}
    <v-btn
      color="accent"
      flat
      @click="closeSnackbar">
      Close
    </v-btn>
  </v-snackbar>
</template>

<script>
	export default {
		computed: {
      snackbar () {
        return this.$store.state.snackbar
      },

      snackbarText () {
        return this.$store.state.snackbarText
      },

      snackbarTimeout () {
        return this.$store.state.snackbarTimeout
      },
		},

		methods: {
			closeSnackbar () {
        this.$store.state.snackbar = false
      },
		}
	}
</script>