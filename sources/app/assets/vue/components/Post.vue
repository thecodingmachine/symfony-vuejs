<template>
  <div class="card w-100 mt-2">
    <div class="card-body">
      {{ message }}
    </div>
  </div>
</template>

<script>
export default {
  name: "Post",
  props: {
    message: {
      type: String,
      required: true
    }
  }
};
</script>
