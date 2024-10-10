<template>
  <div class="request-leave">
    <h1>Request Leave</h1>
    <form @submit.prevent="submitLeaveRequest">
      <div class="form-group">
        <label for="leaveType">Leave Type</label>
        <select v-model="selectedLeaveType" id="leaveType" required @change="updateLeaveInfo">
          <option value="">Select a leave type</option>
          <option v-for="(leave, type) in leaveTypes" :key="type" :value="type">
            {{ type }}
          </option>
        </select>
      </div>

      <transition name="fade">
        <div v-if="selectedLeaveType" class="leave-info">
          <h2>{{ selectedLeaveType }}</h2>
          <div class="info-grid">
            <div class="info-item">
              <i class="fas fa-info-circle"></i>
              <p><strong>Description:</strong> {{ currentLeave.description }}</p>
            </div>
            <div class="info-item">
              <i class="fas fa-user-check"></i>
              <p><strong>Eligibility:</strong> {{ currentLeave.eligibility }}</p>
            </div>
            <div class="info-item">
              <i class="fas fa-calendar-alt"></i>
              <p><strong>Available Days:</strong> {{ currentLeave.days }}</p>
            </div>
          </div>
        </div>
      </transition>

      <div class="form-row">
        <div class="form-group">
          <label for="startDate">Start Date</label>
          <input v-model="startDate" type="date" id="startDate" required>
        </div>
        <div class="form-group">
          <label for="endDate">End Date</label>
          <input v-model="endDate" type="date" id="endDate" required>
        </div>
      </div>

      <div class="form-group">
        <label for="reason">Reason for Leave</label>
        <textarea v-model="reason" id="reason" rows="3" required></textarea>
      </div>

      <div class="form-group">
        <label for="attachments">Attachments (if any)</label>
        <div class="file-input-wrapper">
          <input type="file" id="attachments" multiple @change="handleFileUpload" class="file-input">
          <label for="attachments" class="file-input-label">
            <i class="fas fa-cloud-upload-alt"></i> Choose Files
          </label>
        </div>
        <transition-group name="list" tag="div" class="attachment-list">
          <div v-for="(file, index) in attachments" :key="file.name" class="attachment-item">
            <i class="fas fa-file"></i>
            <span>{{ file.name }}</span>
            <button type="button" @click="removeAttachment(index)" class="remove-btn">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </transition-group>
      </div>

      <button type="submit" :disabled="isSubmitting" class="submit-btn">
        <i class="fas fa-paper-plane"></i>
        {{ isSubmitting ? 'Submitting...' : 'Submit Leave Request' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { API_BASE_URL } from '@/config';
import Swal from 'sweetalert2';

const leaveTypes = {
  'Vacation Leave': {
    description: 'Time off for rest, recreation, or personal business.',
    eligibility: 'All regular employees',
    days: 15
  },
  'Mandatory Leave': {
    description: 'Compulsory leave during specific periods.',
    eligibility: 'All employees',
    days: 5
  },
  'Sick Leave': {
    description: 'Time off due to illness or medical appointments.',
    eligibility: 'All employees',
    days: 15
  },
  'Maternity Leave': {
    description: 'Leave for female employees before and after childbirth.',
    eligibility: 'Female employees who are pregnant',
    days: 105
  },
  'Paternity Leave': {
    description: 'Leave for male employees to support their partner after childbirth.',
    eligibility: 'Male employees with a newborn child',
    days: 7
  },
  'Special Privilege Leave': {
    description: 'Leave for personal milestones, filial obligations, or parental duties.',
    eligibility: 'All regular employees',
    days: 3
  },
  'Solo Parent Leave': {
    description: 'Additional leave for single parents.',
    eligibility: 'Employees who are certified solo parents',
    days: 7
  },
  'Study Leave': {
    description: 'Leave for educational or professional development purposes.',
    eligibility: 'Employees pursuing relevant studies or training',
    days: 'Varies'
  },
  'VAWC Leave': {
    description: 'Leave for victims of violence against women and children.',
    eligibility: 'Employees who are victims of VAWC',
    days: 10
  },
  'Rehabilitation Leave': {
    description: 'Leave for employees who need to undergo rehabilitation for disabilities.',
    eligibility: 'Employees with disabilities requiring rehabilitation',
    days: 'Up to 6 months'
  },
  'Special Leave Benefits for Women': {
    description: 'Leave for women who undergo surgery due to gynecological disorders.',
    eligibility: 'Female employees undergoing specific medical procedures',
    days: 'Up to 2 months'
  },
  'Special Emergency (Calamity) Leave': {
    description: 'Leave during times of natural calamities or disasters.',
    eligibility: 'Employees affected by calamities',
    days: 5
  },
  'Monetization of Leave Credits': {
    description: 'Converting unused leave credits to cash.',
    eligibility: 'Employees with accumulated leave credits',
    days: 'Varies based on accumulated credits'
  },
  'Terminal Leave': {
    description: 'Leave credited to an employee upon retirement or separation from service.',
    eligibility: 'Retiring or separating employees',
    days: 'Based on accumulated leave credits'
  },
  'Adoption Leave': {
    description: 'Leave for employees who legally adopt a child.',
    eligibility: 'Employees who have legally adopted a child',
    days: 60
  }
};

const selectedLeaveType = ref('');
const startDate = ref('');
const endDate = ref('');
const reason = ref('');
const isSubmitting = ref(false);
const attachments = ref([]);

const currentLeave = computed(() => leaveTypes[selectedLeaveType.value] || {});

const updateLeaveInfo = () => {
  startDate.value = '';
  endDate.value = '';
  reason.value = '';
};

const handleFileUpload = (event) => {
  attachments.value = [...attachments.value, ...event.target.files];
};

const removeAttachment = (index) => {
  attachments.value.splice(index, 1);
};

const submitLeaveRequest = async () => {
  isSubmitting.value = true;
  try {
    const user = JSON.parse(localStorage.getItem('user'));
    const response = await fetch(`${API_BASE_URL}/request-leave`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
      credentials: 'include',
      body: JSON.stringify({
        userId: user.id,
        leaveType: selectedLeaveType.value,
        startDate: startDate.value,
        endDate: endDate.value,
        reason: reason.value,
      }),
    });
    const result = await response.json();
    if (result.success) {
      Swal.fire({
        title: 'Leave Request Submitted',
        text: 'Your leave request has been submitted successfully.',
        icon: 'success',
      });
      // Reset form fields
      selectedLeaveType.value = '';
      startDate.value = '';
      endDate.value = '';
      reason.value = '';
    } else {
      throw new Error('Failed to submit leave request');
    }
  } catch (error) {
    console.error('Error submitting leave request:', error);
    Swal.fire({
      title: 'Error',
      text: 'Failed to submit leave request. Please try again.',
      icon: 'error',
    });
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.request-leave {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

h1 {
  color: #2c3e50;
  margin-bottom: 1.5rem;
  text-align: center;
  font-size: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1rem;
}

.form-row .form-group {
  flex: 1;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: #34495e;
  font-weight: bold;
}

select, input, textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

select:focus, input:focus, textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.leave-info {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  transition: all 0.3s ease;
}

.leave-info:hover {
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.leave-info h2 {
  color: #3498db;
  margin-top: 0;
  margin-bottom: 1rem;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.info-item {
  display: flex;
  align-items: flex-start;
}

.info-item i {
  color: #3498db;
  margin-right: 0.5rem;
  margin-top: 0.25rem;
}

.file-input-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
}

.file-input {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  cursor: pointer;
}

.file-input-label {
  display: inline-block;
  padding: 0.75rem 1rem;
  background-color: #3498db;
  color: white;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.file-input-label:hover {
  background-color: #2980b9;
}

.attachment-list {
  margin-top: 1rem;
}

.attachment-item {
  display: flex;
  align-items: center;
  background-color: #f8f9fa;
  padding: 0.5rem;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.attachment-item i {
  margin-right: 0.5rem;
  color: #3498db;
}

.remove-btn {
  background: none;
  border: none;
  color: #e74c3c;
  cursor: pointer;
  margin-left: auto;
}

.submit-btn {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.submit-btn i {
  margin-right: 0.5rem;
}

.submit-btn:hover:not(:disabled) {
  background-color: #2980b9;
}

.submit-btn:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
}

body {
  background-image: url('@/assets/logo.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  min-height: 100vh;
}
</style>
