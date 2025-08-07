import apiClient from './apiClient';

export const authAPI = {
  login: (username: string, password: string) =>
    apiClient.post('/login', {username, password}),
  register: (data: {
    username: string;
    password: string;
    email: string;
    full_name: string;
    phone: string;
  }) => apiClient.post('/register', data),
  getProfile: () => apiClient.get('/profile'),
  updateProfile: (data: {full_name?: string; phone?: string; address?: string}) =>
    apiClient.put('/profile', data),
  changePassword: (data: {
    current_password: string;
    new_password: string;
    confirm_password: string;
  }) => apiClient.post('/change-password', data),
};

export const boatsAPI = {
  getAllBoats: () => apiClient.get('/boats'),
  getBoatById: (id: number) => apiClient.get(`/boats/${id}`),
};

export const bookingsAPI = {
  getAllBookings: () => apiClient.get('/bookings'),
  getBookingById: (id: number) => apiClient.get(`/bookings/${id}`),
  createBooking: (data: {
    schedule_id: number;
    passenger_count: number;
    passengers: Array<{
      full_name: string;
      identity_number?: string;
      phone?: string;
      age?: number;
    }>;
    payment_method: 'transfer' | 'cash';
    notes?: string;
  }) => apiClient.post('/bookings', data),
  cancelBooking: (id: number) => apiClient.post(`/bookings/${id}/cancel`),
};

export const paymentsAPI = {
  getAllPayments: () => apiClient.get('/payments'),
  getPaymentById: (id: number) => apiClient.get(`/payments/${id}`),
  createPayment: (data: FormData) => apiClient.post('/payments', data),
};

export const routesAPI = {
  getAllRoutes: () => apiClient.get('/routes'),
  getRouteById: (id: number) => apiClient.get(`/routes/${id}`),
};

export const schedulesAPI = {
  getAllSchedules: (params?: {
    route_id?: number;
    date_from?: string;
    date_to?: string;
  }) => apiClient.get('/schedules', {params}),
  getScheduleById: (id: number) => apiClient.get(`/schedules/${id}`),
};

export const islandsAPI = {
  getAllIslands: () => apiClient.get('/islands'),
  getIslandById: (id: number) => apiClient.get(`/islands/${id}`),
};

export const galleryAPI = {
  getAllGalleryItems: () => apiClient.get('/gallery'),
  getFeaturedGalleryItems: () => apiClient.get('/gallery/featured'),
  getGalleryCategories: () => apiClient.get('/gallery/categories'),
};

export const faqsAPI = {
  getAllFAQs: () => apiClient.get('/faqs'),
  getFeaturedFAQs: () => apiClient.get('/faqs/featured'),
};

export const testimonialsAPI = {
  getAllTestimonials: () => apiClient.get('/testimonials'),
  getApprovedTestimonials: () => apiClient.get('/testimonials/approved'),
  createTestimonial: (data: {content: string; rating: number}) =>
    apiClient.post('/testimonials', data),
};