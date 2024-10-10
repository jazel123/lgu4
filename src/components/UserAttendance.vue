 <template>
  <div class="user-attendance">
    <h1>Monthly Attendance</h1>
    <div class="month-selector">
      <button @click="previousMonth" :disabled="isLoading">&lt; Previous</button>
      <h2>{{ currentMonthYear }}</h2>
      <button @click="nextMonth" :disabled="isLoading">Next &gt;</button>
    </div>
    <div v-if="isLoading" class="loading">Loading...</div>
    <table v-else class="attendance-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Day</th>
          <th>Status</th>
          <th>Check-In</th>
          <th>Check-Out</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="day in daysInMonth" :key="day.date" :class="{ 'weekend': day.isWeekend, 'holiday': day.isHoliday }">
          <td>{{ day.date }}</td>
          <td>{{ day.dayName }}</td>
          <td :class="day.status.toLowerCase()">{{ day.status }}</td>
          <td>{{ day.checkIn || '-' }}</td>
          <td>{{ day.checkOut || '-' }}</td>
        </tr>
      </tbody>
    </table>
    <div class="summary">
      <p>Present: {{ presentDays }}</p>
      <p>Absent: {{ absentDays }}</p>
      <p>Weekends: {{ weekendDays }}</p>
      <p>Holidays: {{ holidayDays }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { API_BASE_URL } from '@/config';

const currentDate = ref(new Date());
const attendanceData = ref([]);
const isLoading = ref(true);

// Add a function to check if a date is a holiday (you can customize this as needed)
const isHoliday = (date) => {
  // This is a placeholder. In a real application, you would fetch holidays from an API or a more comprehensive list
  const holidays = [
    { date: '2023-01-01', name: "New Year's Day" },
    { date: '2023-12-25', name: 'Christmas Day' },
    // Add more holidays as needed
  ];
  return holidays.find(holiday => holiday.date === date.toISOString().split('T')[0]);
};

const daysInMonth = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  
  return Array.from({ length: daysInMonth }, (_, i) => {
    const date = new Date(year, month, i + 1);
    const dayData = attendanceData.value.find(d => d.date === `${year}-${String(month + 1).padStart(2, '0')}-${String(i + 1).padStart(2, '0')}`);
    const isWeekend = date.getDay() === 0 || date.getDay() === 6;
    const holiday = isHoliday(date);
    
    return {
      date: `${year}-${String(month + 1).padStart(2, '0')}-${String(i + 1).padStart(2, '0')}`,
      dayName: date.toLocaleDateString('en-US', { weekday: 'short' }),
      isWeekend,
      isHoliday: holiday,
      status: dayData ? dayData.status : (holiday ? 'Holiday' : (isWeekend ? 'Weekend' : 'Absent')),
      checkIn: dayData ? dayData.checkIn : null,
      checkOut: dayData ? dayData.checkOut : null
    };
  });
});

const currentMonthYear = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const presentDays = computed(() => daysInMonth.value.filter(day => day.status === 'Present').length);
const absentDays = computed(() => daysInMonth.value.filter(day => day.status === 'Absent').length);
const weekendDays = computed(() => daysInMonth.value.filter(day => day.isWeekend).length);
const holidayDays = computed(() => daysInMonth.value.filter(day => day.isHoliday).length);

const previousMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
  fetchAttendanceData();
};

const nextMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
  fetchAttendanceData();
};

const fetchAttendanceData = async () => {
  isLoading.value = true;
  try {
    const user = JSON.parse(localStorage.getItem('user'));
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth() + 1;
    const response = await fetch(`${API_BASE_URL}/attendance?userId=${user.id}&year=${year}&month=${month}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('authToken')}`,
      },
      credentials: 'include',
    });
    const data = await response.json();
    attendanceData.value = data;
  } catch (error) {
    console.error('Error fetching attendance data:', error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchAttendanceData);
</script>

<style scoped>
.user-attendance {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  text-align: center;
  color: #2c3e50;
}

.month-selector {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.month-selector button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.month-selector button:hover:not(:disabled) {
  background-color: #2980b9;
}

.month-selector button:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.loading {
  text-align: center;
  font-size: 1.2rem;
  margin: 2rem 0;
}

.attendance-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
}

.attendance-table th, .attendance-table td {
  border: 1px solid #ddd;
  padding: 0.5rem;
  text-align: center;
}

.attendance-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}

.weekend {
  background-color: #f0f0f0;
}

.holiday {
  background-color: #ffe6e6;
}

.present {
  color: #27ae60;
}

.absent {
  color: #e74c3c;
}

.weekend, .holiday {
  color: #7f8c8d;
}

.summary {
  display: flex;
  justify-content: space-around;
  margin-top: 1rem;
  font-weight: bold;
}

@media (max-width: 768px) {
  .attendance-table {
    font-size: 0.8rem;
  }
  
  .attendance-table th, .attendance-table td {
    padding: 0.3rem;
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
