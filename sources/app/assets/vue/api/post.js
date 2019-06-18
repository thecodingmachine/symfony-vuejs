import axios from "axios";

export default {
  create(message) {
    return axios.post("/api/post/create", {
      message: message
    });
  },
  posts() {
    return axios.get("/api/posts");
  }
};