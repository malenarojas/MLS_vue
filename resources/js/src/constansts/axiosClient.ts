import axios, { type AxiosInstance } from "axios";

const apiClient: AxiosInstance = axios.create({
    //baseURL: "https://rmb.targetbit.com",
   //baseURL: "https://monorex.targetbit.com",
    baseURL: "http://127.0.0.1:8000",
    timeout: 50000,
    headers: {
        "Content-Type": "application/json",
    },
});

export default apiClient;
