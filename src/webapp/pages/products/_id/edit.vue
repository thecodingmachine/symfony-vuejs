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
        id="input-group-new-pictures"
        :label="$t('pages.products.form.new_pictures.label')"
        label-for="input-new-pictures"
      >
        <b-form-file
          id="input-new-pictures"
          v-model="form.newPictures"
          :placeholder="$t('common.multiple_files.placeholder')"
          :drop-placeholder="$t('common.multiple_files.drop_placeholder')"
          :browse-text="$t('common.browse_files_text')"
          multiple
          :state="formState('product_picture')"
        ></b-form-file>
        <b-button class="mt-2" @click="form.newPictures = []">
          {{ $t('common.reset_files_button') }}
        </b-button>
        <div class="mt-3">
          <FilesList :files="form.newPictures" />
        </div>
        <b-form-invalid-feedback :state="formState('product_picture')">
          <ErrorsList :errors="formErrors('product_picture')" />
        </b-form-invalid-feedback>
      </b-form-group>
      <div v-if="form.pictures.length > 0">
        <b-card-group deck>
          <b-card
            v-for="(picture, index) in form.pictures"
            :key="index"
            style="max-width: 200px"
            :title="picture"
            :img-src="$config.productPictureURL + picture"
            :img-alt="picture"
            :img-top="true"
            img-width="200"
            img-height="134"
          >
            <b-button
              variant="primary"
              @click="addPictureToDeleteList(picture)"
              >{{ $t('common.delete_button') }}</b-button
            >
          </b-card>
        </b-card-group>
        <b-button class="mt-2 mb-2" @click="resetPicturesToDelete">
          {{ $t('common.reset_files_button') }}
        </b-button>
      </div>
      <b-button type="submit" variant="primary" :disabled="isFormReadOnly">
        <b-spinner v-show="isFormReadOnly" small type="grow"></b-spinner>
        {{
          isFormReadOnly
            ? $t('pages.products.form.update_submitting')
            : $t('pages.products.form.update_submit')
        }}
      </b-button>
    </b-form>
  </b-container>
</template>

<script>
import Form from '@/mixins/form'
import ProductQuery from '@/services/queries/products/product.query.gql'
import UpdateProductMutation from '@/services/mutations/products/update_product.mutation.gql'
import ErrorsList from '@/components/forms/ErrorsList'
import FilesList from '@/components/forms/FilesList'

export default {
  components: { FilesList, ErrorsList },
  mixins: [Form],
  async asyncData(context) {
    try {
      const result = await context.app.$graphql.request(ProductQuery, {
        id: context.route.params.id,
      })

      return {
        form: {
          name: result.product.name,
          price: result.product.price,
          pictures: result.product.pictures || [],
          newPictures: [],
          picturesToDelete: [],
        },
      }
    } catch (e) {
      context.error(e)
    }
  },
  methods: {
    async onSubmit() {
      this.resetFormErrors()
      this.isFormReadOnly = true

      try {
        const result = await this.$graphql.request(UpdateProductMutation, {
          productId: this.$route.params.id,
          name: this.form.name,
          price: this.form.price,
          newPictures: this.form.newPictures,
          picturesToDelete: this.form.picturesToDelete,
        })

        this.$router.push(
          this.localePath({
            name: 'products-id',
            params: { id: result.updateProduct.id },
          })
        )
      } catch (e) {
        this.hydrateFormErrors(e)
        this.isFormReadOnly = false
      }
    },
    addPictureToDeleteList(picture) {
      this.form.picturesToDelete.push(picture)

      this.form.pictures = this.form.pictures.filter((value) => {
        return value !== picture
      })
    },
    resetPicturesToDelete() {
      this.form.pictures = this.form.pictures.concat(this.form.picturesToDelete)
      this.form.picturesToDelete = []
    },
  },
}
</script>
