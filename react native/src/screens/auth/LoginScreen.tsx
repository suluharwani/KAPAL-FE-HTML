import React, {useState} from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Alert,
} from 'react-native';
import {useDispatch, useSelector} from 'react-redux';
import {loginStart, loginSuccess, loginFailure} from '../../store/authSlice';
import {authAPI} from '../../api/endpoints';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {colors, globalStyles} from '../../theme/styles';
import Icon from 'react-native-vector-icons/MaterialIcons';

const LoginScreen = ({navigation}: any) => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const dispatch = useDispatch();
  const loading = useSelector((state: any) => state.auth.loading);

  const handleLogin = async () => {
    if (!username || !password) {
      Alert.alert('Error', 'Please fill all fields');
      return;
    }

    dispatch(loginStart());
    try {
      const response = await authAPI.login(username, password);
      
      // Check if user is customer
      if (response.data.user.role !== 'customer') {
        throw new Error('Only customers can login to this app');
      }

      await AsyncStorage.setItem('auth_token', response.data.token);
      dispatch(loginSuccess(response.data));
    } catch (error: any) {
      dispatch(loginFailure(error.message || 'Login failed'));
      Alert.alert('Error', error.message || 'Login failed');
    }
  };

  return (
    <View style={[globalStyles.container, styles.container]}>
      <Text style={globalStyles.title}>Login</Text>
      
      <View style={styles.inputContainer}>
        <Icon name="person" size={20} color={colors.gray} style={styles.icon} />
        <TextInput
          style={styles.input}
          placeholder="Username"
          value={username}
          onChangeText={setUsername}
          autoCapitalize="none"
        />
      </View>
      
      <View style={styles.inputContainer}>
        <Icon name="lock" size={20} color={colors.gray} style={styles.icon} />
        <TextInput
          style={styles.input}
          placeholder="Password"
          value={password}
          onChangeText={setPassword}
          secureTextEntry={!showPassword}
        />
        <TouchableOpacity 
          onPress={() => setShowPassword(!showPassword)}
          style={styles.eyeIcon}>
          <Icon 
            name={showPassword ? 'visibility' : 'visibility-off'} 
            size={20} 
            color={colors.gray} 
          />
        </TouchableOpacity>
      </View>
      
      <TouchableOpacity
        style={globalStyles.button}
        onPress={handleLogin}
        disabled={loading}>
        <Text style={globalStyles.buttonText}>
          {loading ? 'Loading...' : 'Login'}
        </Text>
      </TouchableOpacity>
      
      <TouchableOpacity
        onPress={() => navigation.navigate('Register')}
        style={styles.registerLink}>
        <Text style={styles.registerText}>
          Don't have an account? <Text style={styles.registerHighlight}>Register</Text>
        </Text>
      </TouchableOpacity>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    justifyContent: 'center',
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.white,
    borderRadius: 8,
    paddingHorizontal: 12,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: colors.gray,
  },
  input: {
    flex: 1,
    height: 50,
    paddingHorizontal: 10,
  },
  icon: {
    marginRight: 10,
  },
  eyeIcon: {
    padding: 10,
  },
  registerLink: {
    marginTop: 20,
    alignItems: 'center',
  },
  registerText: {
    color: colors.dark,
  },
  registerHighlight: {
    color: colors.primary,
    fontWeight: 'bold',
  },
});

export default LoginScreen;