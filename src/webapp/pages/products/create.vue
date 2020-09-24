<template>
  <b-container>
    <b-overlay :show="blockUserInputs">
      <b-form @submit.stop.prevent="onSubmit">
        <b-form-group
          id="input-group-name"
          label="Name"
          label-for="input-name"
          description="A unique name for your product."
        >
          <b-form-input
            id="input-name"
            v-model="form.name"
            type="text"
            placeholder="Enter a name"
            :state="nameValidation && asyncValidation.isNameAvailable"
            autofocus
            required
            trim
            debounce="1000"
          >
          </b-form-input>
          <b-form-invalid-feedback v-if="!nameValidation">
            The name must be 1-255 characters long.
          </b-form-invalid-feedback>
          <b-form-invalid-feedback v-if="!asyncValidation.isNameAvailable">
            The name must be unique.
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group
          id="input-group-price"
          label="Price"
          label-for="input-price"
        >
          <b-form-input
            id="input-price"
            v-model="form.price"
            type="number"
            :state="priceValidation"
            required
            placeholder="Enter a price"
          >
          </b-form-input>
          <b-form-invalid-feedback :state="priceValidation">
            The price must be positive.
          </b-form-invalid-feedback>
        </b-form-group>

        <b-overlay :show="$apollo.queries.companies.loading">
          <b-form-group
            id="input-group-company"
            label="Company"
            label-for="input-company"
          >
            <b-form-select
              id="input-company"
              v-model="form.companyId"
              required
              placeholder="Select a company"
              value-field="id"
              text-field="value"
              :options="companies"
            >
            </b-form-select>
          </b-form-group>
        </b-overlay>

        <b-button type="submit" variant="primary">Create</b-button>
      </b-form>
    </b-overlay>
  </b-container>
</template>

<script>
import CompaniesQuery from '@/services/queries/companies/companies.query.gql'
import CheckProductNameAvailabilityQuery from '@/services/queries/products/check_product_name_availability.query.gql'
import CreateProductMutation from '@/services/mutations/products/create_product.mutation.gql'

export default {
  data() {
    return {
      blockUserInputs: false,
      form: {
        name: '',
        price: 1,
        companyId: 240,
      },
      asyncValidation: {
        isNameAvailable: true,
      },
      companies: [],
      checkProductName: null,
    }
  },
  apollo: {
    companies: {
      prefetch: true,
      manual: true,
      query: CompaniesQuery,
      result({ data, loading }) {
        if (!loading) {
          this.companies = data.companies.items.map(({ id, name }) => ({
            id,
            value: name,
          }))
        }
      },
    },
    checkProductName: {
      query: CheckProductNameAvailabilityQuery,
      variables() {
        return {
          name: this.form.name,
        }
      },
      result({ data, loading }) {
        if (!loading) {
          this.asyncValidation.isNameAvailable = data.checkProductName
        }
      },
    },
  },
  computed: {
    nameValidation() {
      return this.form.name.length > 0 && this.form.name.length <= 255
    },
    priceValidation() {
      return this.form.price > 0
    },
  },
  methods: {
    async onSubmit() {
      this.blockUserInputs = true

      const { data } = await this.$apollo.mutate({
        mutation: CreateProductMutation,
        variables: {
          name: this.form.name,
          price: this.form.price,
          companyId: this.form.companyId,
        },
      })

      this.blockUserInputs = false

      this.$router.push(`/products/ + ${data.createProduct.id}`)
    },
  },
}
</script>
