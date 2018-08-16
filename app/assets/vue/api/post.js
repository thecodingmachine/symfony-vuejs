import axios from 'axios';

export default {
    create (message) {
        return axios.post(
            '/api/post/create',
            {
                message: message,
            }
        );
    },
    getAll () {
        return axios.get('/api/posts');
    },
}