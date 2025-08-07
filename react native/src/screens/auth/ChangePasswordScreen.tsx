import React, {useState} from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  Alert,
} from 'react-native';
import {globalStyles, colors} from '../../theme/styles';
import {authAPI} from '../../api/endpoints';
import Input from '../../components/ui/Input';
import Button from '../../components/ui/Button';
import Icon from 'react-native-vector-icons/MaterialIcons';

const ChangePasswordScreen = () => {
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [showCurrentPassword, setShowCurrentPassword] = useState(false);
  const [showNewPassword, setShowNewPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const handleSubmit = async () => {
    if (!currentPassword || !newPassword || !confirmPassword) {
      Alert.alert('Error', 'Please fill all fields');
      return;
    }

    if (newPassword !== confirmPassword) {
      Alert.alert('Error', 'New passwords do not match');
      return;
    }

    try {
      setLoading(true);
      await authAPI.changePassword({
        current_password: currentPassword,
        new_password: newPassword,
        confirm_password: confirmPassword,
      });
      Alert.alert('Success', 'Password changed successfully');
      setCurrentPassword('');
      setNewPassword('');
      setConfirmPassword('');
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to change password. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={globalStyles.container}>
      <Text style={globalStyles.title}>Change Password</Text>
      
      <Input
        label="Current Password"
        value={currentPassword}
        onChangeText={setCurrentPassword}
        secureTextEntry={!showCurrentPassword}
        leftIcon={<Icon name="lock" size={20} color={colors.gray} />}
        rightIcon={
          <TouchableOpacity
            onPress={() => setShowCurrentPassword(!showCurrentPassword)}>
            <Icon
              name={showCurrentPassword ? 'visibility' : 'visibility-off'}
              size={20}
              color={colors.gray}
            />
          </TouchableOpacity>
        }
      />
      
      <Input
        label="New Password"
        value={newPassword}
        onChangeText={setNewPassword}
        secureTextEntry={!showNewPassword}
        leftIcon={<Icon name="lock-outline" size={20} color={colors.gray} />}
        rightIcon={
          <TouchableOpacity
            onPress={() => setShowNewPassword(!showNewPassword)}>
            <Icon
              name={showNewPassword ? 'visibility' : 'visibility-off'}
              size={20}
              color={colors.gray}
            />
          </TouchableOpacity>
        }
      />
      
      <Input
        label="Confirm New Password"
        value={confirmPassword}
        onChangeText={setConfirmPassword}
        secureTextEntry={!showConfirmPassword}
        leftIcon={<Icon name="lock-outline" size={20} color={colors.gray} />}
        rightIcon={
          <TouchableOpacity
            onPress={() => setShowConfirmPassword(!showConfirmPassword)}>
            <Icon
              name={showConfirmPassword ? 'visibility' : 'visibility-off'}
              size={20}
              color={colors.gray}
            />
          </TouchableOpacity>
        }
      />
      
      <Button
        title={loading ? 'Processing...' : 'Change Password'}
        onPress={handleSubmit}
        disabled={loading}
      />
    </ScrollView>
  );
};

export default ChangePasswordScreen;