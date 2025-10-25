// frontend/services/api.js
const API_BASE_URL = 'http://localhost/Nueva-WEB/api';

async function fetchData(endpoint) {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`);
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    return response.json();
}

async function getProducts() {
    return fetchData('products');
}

async function getProductById(productId) {
    return fetchData(`products/${productId}`);
}

async function loginUser(credentials) {
    const response = await fetch(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials),
    });
    if (!response.ok) {
        throw new Error('Login failed');
    }
    return response.json();
}

async function registerUser(userData) {
    const response = await fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData),
    });
    if (!response.ok) {
        throw new Error('Registration failed');
    }
    return response.json();
}

async function getUserProfile(userId) {
    return fetchData(`users/${userId}`);
}

export { getProducts, getProductById, loginUser, registerUser, getUserProfile };