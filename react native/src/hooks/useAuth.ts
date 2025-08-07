import {useEffect} from 'react';
import {useDispatch, useSelector} from 'react-redux';
import {RootState} from '../store/store';
import {setUser} from '../store/authSlice';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {authAPI} from '../api/endpoints';

const useAuth = () => {
  const dispatch = useDispatch();
  const {isAuthenticated, user} = useSelector((state: RootState) => state.auth);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        const token = await AsyncStorage.getItem('auth_token');
        if (token) {
          const response = await authAPI.getProfile();
          if (response.data.role === 'customer') {
            dispatch(setUser(response.data));
          } else {
            await AsyncStorage.removeItem('auth_token');
          }
        }
      } catch (error) {
        console.error('Auth check error:', error);
        await AsyncStorage.removeItem('auth_token');
      }
    };

    checkAuth();
  }, [dispatch]);

  return {isAuthenticated, user};
};

export default useAuth;