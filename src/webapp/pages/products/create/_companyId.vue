<template>
  <b-container>
    <b-form class="mt-3" @submit.stop.prevent="onSubmit">
      <b-form-group
        id="input-group-name"
        :label="$t('pages.products.form.name.label') + ' *'"
        label-for="input-name"
      >
        <b-form-input
          id="input-name"
          v-model="form.name"
          type="text"
          :placeholder="$t('pages.products.form.name.placeholder')"
          autofocus
          trim
          required
          :disabled="isFormReadOnly"
          :state="formState('name')"
        />
        <b-form-invalid-feedback :state="formState('name')">
          <ErrorsList :errors="formErrors('name')" />
        </b-form-invalid-feedback>
      </b-form-group>
      <b-form-group
        id="input-group-price"
        :label="$t('pages.products.form.price.label') + ' *'"
        label-for="input-price"
      >
        <b-form-input
          id="input-price"
          v-model="form.price"
          type="number"
          :placeholder="$t('pages.products.form.price.placeholder')"
          trim
          number
          required
          step="any"
          :disabled="isFormReadOnly"
          :state="formState('price')"
        />
        <b-form-invalid-feedback :state="formState('price')">
          <ErrorsList :errors="formErrors('price')" />
        </b-form-invalid-feedback>
      </b-form-group>
      <b-form-group
        id="input-group-pictures"
        :label="$t('pages.products.form.pictures.label')"
        label-for="input-pictures"
      >
        <b-form-file
          id="input-pictures"
          v-model="form.pictures"
          :placeholder="$t('common.multiple_files.placeholder')"
          :drop-placeholder="$t('common.multiple_files.drop_placeholder')"
          :browse-text="$t('common.browse_files_text')"
          multiple
          :state="formState('product_picture')"
        ></b-form-file>
        <b-button class="mt-2" @click="form.pictures = []">
          {{ $t('common.reset_files_button') }}
        </b-button>
        <div class="mt-3">
          <FilesList :files="form.pictures" />
        </div>
        <b-form-invalid-feedback :state="formState('product_picture')">
          <ErrorsList :errors="formErrors('product_picture')" />
        </b-form-invalid-feedback>
      </b-form-group>
      <b-button type="submit" variant="primary" :disabled="isFormReadOnly">
        <b-spinner v-show="isFormReadOnly" small type="grow"></b-spinner>
        {{
          isFormReadOnly
            ? $t('pages.products.form.create_submitting')
            : $t('pages.products.form.create_submit')
        }}
      </b-button>
    </b-form>
  </b-container>
</template>

<script>
import Form from '@/mixins/form'
import CreateProductMutation from '@/services/mutations/products/create_product.mutation.gql'
import ErrorsList from '@/components/forms/ErrorsList'
import FilesList from '@/components/forms/FilesList'

export default {
  components: { FilesList, ErrorsList },
  mixins: [Form],
  data() {
    return {
      form: {
        name: '',
        price: 0,
        pictures: [],
      },
    }
  },
  methods: {
    async onSubmit() {
      this.resetFormErrors()
      this.isFormReadOnly = true

      try {
        const result = await this.$graphql.request(CreateProductMutation, {
          name: this.form.name,
          price: this.form.price,
          companyId: this.$route.params.companyId,
          pictures: this.form.pictures,
        })

        this.$router.push(
          this.localePath({
            name: 'products-id',
            params: { id: result.createProduct.id },
          })
        )
      } catch (e) {
        this.hydrateFormErrors(e)
        this.isFormReadOnly = false
      }
    },
  },
}
</script>
