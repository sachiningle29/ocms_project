export default {
    state: {
        isAuthenticated: false,
        userRole: null
    },
    mutations: {
        SET_AUTH(state, { isAuthenticated, userRole }) {
            state.isAuthenticated = isAuthenticated;
            state.userRole = userRole;
        },
        CLEAR_AUTH(state) {
            state.isAuthenticated = false;
            state.userRole = null;
        }
    },
    actions: {
        login({ commit }, { email, password, role }) {
            // Your login logic here
            return new Promise((resolve, reject) => {
                // Simulate API call
                setTimeout(() => {
                    if (email && password) {
                        commit('SET_AUTH', { 
                            isAuthenticated: true, 
                            userRole: role 
                        });
                        resolve();
                    } else {
                        reject(new Error('Invalid credentials'));
                    }
                }, 500);
            });
        },
        logout({ commit }) {
            commit('CLEAR_AUTH');
        }
    },
    getters: {
        isAuthenticated: state => state.isAuthenticated,
        isAdmin: state => state.userRole === 'admin',
        isUser: state => state.userRole === 'user'
    }
};