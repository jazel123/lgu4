<template>
  <div class="register-container">
    <div class="register-card">
      <h1 class="register-title">Create Your Account</h1>
      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <label for="firstName">First Name</label>
          <input id="firstName" v-model="firstName" type="text" required placeholder="Enter your first name">
        </div>
        <div class="form-group">
          <label for="middleName">Middle Name</label>
          <input id="middleName" v-model="middleName" type="text" placeholder="Enter your middle name">
        </div>
        <div class="form-group">
          <label for="lastName">Last Name</label>
          <input id="lastName" v-model="lastName" type="text" required placeholder="Enter your last name">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" v-model="email" type="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" v-model="password" type="password" required placeholder="Enter your password">
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input id="confirmPassword" v-model="confirmPassword" type="password" required placeholder="Confirm your password">
        </div>
        <div class="form-group">
          <label for="birthdate">Birthdate</label>
          <input id="birthdate" v-model="birthdate" type="date" required>
        </div>
        <div class="form-group">
          <label for="employeeId">Employee ID</label>
          <input id="employeeId" v-model="employeeId" type="text" required placeholder="Enter your employee ID">
        </div>
        <div class="form-group">
          <label for="mobile">Mobile Number</label>
          <input id="mobile" v-model="mobile" type="tel" required placeholder="Enter your mobile number">
        </div>
        <div class="form-group">
          <label for="department">Department</label>
          <input id="department" v-model="department" type="text" required placeholder="Enter your department">
        </div>
        <div class="form-group">
          <label for="role">Role</label>
          <select id="role" v-model="role" required>
            <option value="">Select a role</option>
            <option value="Employee">Employee</option>
            
          </select>
        </div>
        <button type="submit" class="register-button" :disabled="isLoading">
          {{ isLoading ? 'Registering...' : 'Register' }}
        </button>
      </form>
      <p class="login-link">
        Already have an account? <router-link to="/">Login here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { API_BASE_URL } from '@/config';

const router = useRouter();
const firstName = ref('');
const middleName = ref('');
const lastName = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const birthdate = ref('');
const employeeId = ref('');
const mobile = ref('');
const department = ref('');
const role = ref('');
const isLoading = ref(false);

const handleRegister = async () => {
  if (password.value !== confirmPassword.value) {
    alert("Passwords don't match");
    return;
  }

  isLoading.value = true;
  try {
    const response = await fetch(`${API_BASE_URL}/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify({
        firstName: firstName.value,
        middleName: middleName.value,
        lastName: lastName.value,
        email: email.value,
        password: password.value,
        birthdate: birthdate.value,
        employeeId: employeeId.value,
        mobile: mobile.value,
        department: department.value,
        role: role.value,
      }),
    });

    if (!response.ok) {
      const errorText = await response.text();
      console.error('Server response:', errorText);
      throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
    }

    const data = await response.json();
    console.log('Registration response:', data);

    alert('Registration successful! Please login.');
    router.push('/');
  } catch (error) {
    console.error('Registration error:', error);
    alert(error.message || 'An error occurred during registration.');
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #3498db, #8e44ad);
}

.register-card {
  background-color: white;
  border-radius: 10px;
  padding: 2rem;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 500px;
  animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateX(-20px); }
  to { opacity: 1; transform: translateX(0); }
}

.register-title {
  text-align: center;
  color: #2c3e50;
  margin-bottom: 2rem;
}

.register-form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: #34495e;
}

input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #bdc3c7;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

input:focus {
  outline: none;
  border-color: #3498db;
}

.register-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.register-button:hover:not(:disabled) {
  background-color: #2980b9;
}

.register-button:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
}

.login-link {
  text-align: center;
  margin-top: 1rem;
  color: #7f8c8d;
}

.login-link a {
  color: #3498db;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>