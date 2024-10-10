<template>
  <div class="background">
    <div class="user-profile">
      <div class="profile-header">
        <div class="profile-picture" @click="triggerFileInput">
          <img :src="profilePicture" alt="Profile Picture" />
          <div class="overlay">
            <span>Change Picture</span>
          </div>
        </div>
        <input
          type="file"
          ref="fileInput"
          @change="handleFileUpload"
          accept="image/*"
          style="display: none"
        />
        <h1>{{ profile.lastName }}, {{ profile.firstName }}</h1>
        <p class="job-title">{{ profile.position }} at {{ profile.department }}</p>
      </div>
      <form @submit.prevent="updateProfile" class="profile-form">
        <div class="form-section" v-for="(section, sectionName) in formSections" :key="sectionName">
          <h2>{{ section.title }}</h2>
          <div class="form-row" v-for="(row, rowIndex) in section.rows" :key="rowIndex">
            <div class="form-group" v-for="field in row" :key="field.name">
              <label :for="field.name">{{ field.label }}</label>
              <input v-if="field.type !== 'select'" 
                v-model="profile[field.name]" 
                :type="field.type" 
                :id="field.name" 
                required 
              />
              <select v-else v-model="profile[field.name]" :id="field.name" required>
                <option v-for="option in field.options" :key="option" :value="option">{{ option }}</option>
              </select>
            </div>
          </div>
        </div>
        <button type="submit" :disabled="isLoading" class="submit-button">
          <span v-if="!isLoading">Update Profile</span>
          <span v-else class="loader"></span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import { API_BASE_URL } from '@/config';

const profile = ref({});
const isLoading = ref(false);
const profilePicture = ref('https://via.placeholder.com/150');
const fileInput = ref(null);

const formSections = {
  personalInfo: {
    title: 'Personal Information',
    rows: [
      [
        { name: 'firstName', label: 'First Name', type: 'text' },
        { name: 'lastName', label: 'Last Name', type: 'text' },
      ],
      [
        { name: 'employeeId', label: 'Employee Id.', type: 'text' },
        { name: 'gender', label: 'Gender', type: 'select', options: ['Male', 'Female', 'Other'] },
      ],
      [
        { name: 'age', label: 'Age', type: 'number' },
        { name: 'birthDate', label: 'Birth Date', type: 'date' },
      ],
      [
        { name: 'civilStatus', label: 'Civil Status', type: 'select', options: ['Single', 'Married', 'Divorced', 'Widowed'] },
      ],
    ],
  },
  contactInfo: {
    title: 'Contact Information',
    rows: [
      [
        { name: 'contactNo', label: 'Contact No.', type: 'tel' },
        { name: 'email', label: 'Email', type: 'email' },
      ],
    ],
  },
  employmentInfo: {
    title: 'Employment Information',
    rows: [
      [
        { name: 'department', label: 'Department', type: 'text' },
        { name: 'position', label: 'Position', type: 'text' },
      ],
    ],
  },
};

const fetchProfileData = async () => {
  try {
    const token = localStorage.getItem('authToken');
    console.log('Auth Token:', token);

    const response = await fetch(`${API_BASE_URL}/user-profile`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      credentials: 'include',
    });

    console.log('Response status:', response.status);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log('Parsed data:', data);

    if (data.success) {
      profile.value = data.user;
      profilePicture.value = data.user.profile_picture || 'https://via.placeholder.com/150';
    } else {
      throw new Error(data.message || 'Failed to fetch profile data');
    }
  } catch (error) {
    console.error('Error fetching profile data:', error);
    Swal.fire({
      title: 'Error',
      text: `Failed to load profile data: ${error.message}`,
      icon: 'error',
      confirmButtonText: 'OK',
    });
  }
};

const updateProfile = async () => {
  isLoading.value = true;
  try {
    const response = await fetch(`${API_BASE_URL}/update-profile`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
      credentials: 'include',
      body: JSON.stringify(profile.value),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    Swal.fire({
      title: 'Profile Updated',
      text: 'Your profile has been successfully updated!',
      icon: 'success',
      confirmButtonText: 'OK',
    });
  } catch (error) {
    console.error('Error updating profile:', error);
    Swal.fire({
      title: 'Error',
      text: 'Failed to update profile. Please try again later.',
      icon: 'error',
      confirmButtonText: 'OK',
    });
  } finally {
    isLoading.value = false;
  }
};

const triggerFileInput = () => {
  fileInput.value.click();
};

const handleFileUpload = async (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      profilePicture.value = e.target.result;
    };
    reader.readAsDataURL(file);

    // Upload the file to the server
    const formData = new FormData();
    formData.append('profilePicture', file);

    try {
      const response = await fetch(`${API_BASE_URL}/upload-profile-picture`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
        },
        credentials: 'include',
        body: formData,
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      profilePicture.value = data.profilePictureUrl;
    } catch (error) {
      console.error('Error uploading profile picture:', error);
      Swal.fire({
        title: 'Error',
        text: 'Failed to upload profile picture. Please try again later.',
        icon: 'error',
        confirmButtonText: 'OK',
      });
    }
  }
};

onMounted(fetchProfileData);
</script>

<style scoped>
.background {
  min-height: 100vh;
  background-image: url('@/assets/logo.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
}

.background::before {
  content: "";
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2V6h4V4H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  pointer-events: none;
}

@keyframes gradientAnimation {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.user-profile {
  max-width: 800px;
  width: 100%;
  background-color: rgba(255, 255, 255, 0.9);
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  padding: 2rem;
}

.profile-header {
  text-align: center;
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e0e0e0;
}

.profile-picture {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto 1rem;
  border-radius: 50%;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.profile-picture img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.profile-picture .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
}

.profile-picture:hover .overlay {
  opacity: 1;
}

.profile-picture .overlay span {
  color: white;
  font-size: 0.9rem;
}

h1 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 2rem;
}

h2 {
  color: #3498db;
  margin-bottom: 1rem;
  font-size: 1.5rem;
}

.job-title {
  color: #7f8c8d;
  font-size: 1.1rem;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.form-section {
  background-color: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
}

.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}

label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #34495e;
}

input, select {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s, box-shadow 0.3s;
}

input:focus, select:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.submit-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 1rem;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 1rem;
}

.submit-button:hover:not(:disabled) {
  background-color: #2980b9;
}

.submit-button:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
}

.loader {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .background {
    padding: 1rem;
  }

  .user-profile {
    padding: 1rem;
  }

  .form-row {
    flex-direction: column;
  }
}
</style>