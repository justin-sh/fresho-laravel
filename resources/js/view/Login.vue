<template>
    <div class="login-wrapper d-flex align-items-center justify-content-center vh90">
        <div class="card login-card shadow-lg p-4 border-0">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">Welcome Back 👋</h2>
                <p class="text-muted">Sign in to continue</p>
            </div>

            <form @submit.prevent="handleLogin">
                <div class="form-floating mb-3">
                    <input
                        type="email"
                        id="email"
                        v-model="email"
                        class="form-control rounded-3"
                        placeholder="name@example.com"
                        required
                    />
                    <label for="email">Email address</label>
                </div>

                <div class="form-floating mb-3">
                    <input
                        type="password"
                        id="password"
                        v-model="password"
                        class="form-control rounded-3"
                        placeholder="Password"
                        required
                    />
                    <label for="password">Password</label>
                </div>

                <div class="d-grid mb-3">
                    <button
                        type="submit"
                        class="btn btn-primary btn-lg rounded-3 fw-semibold"
                        :disabled="loading"
                    >
                        <span v-if="!loading">Login</span>
                        <div v-else class="spinner-border spinner-border-sm text-light" role="status"></div>
                    </button>
                </div>

                <div class="text-center">
                    <small class="text-muted">Don’t have an account? <a href="https://app.fresho.com/user/new" class="text-decoration-none">Sign up at Fresho</a></small>
                </div>

                <div v-if="errorMessage" class="alert alert-danger mt-3 text-center rounded-3 py-2">
                    {{ errorMessage }}
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
    loading.value = true
    errorMessage.value = ''
    await new Promise(r => setTimeout(r, 1000)) // simulate request

    if (email.value === 'admin@example.com' && password.value === '123456') {
        alert('🎉 Login successful!')
    } else {
        errorMessage.value = 'Invalid email or password.'
    }

    loading.value = false
}
</script>

<style scoped>
.login-wrapper {
    animation: fadeIn 1.2s ease;
}

.login-card {
    width: 22rem;
    background: #fff;
    border-radius: 1.5rem;
    animation: slideUp 0.8s ease;
}

.btn-primary {
    transition: all 0.3s ease;
}
.btn-primary:hover {
    background-color: #563d7c;
    transform: translateY(-2px);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideUp {
    from { transform: translateY(40px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>
