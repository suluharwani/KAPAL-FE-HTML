import React, {useState, useEffect} from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  Image,
  TouchableOpacity,
  Alert,
} from 'react-native';
import {globalStyles, colors} from '../../theme/styles';
import {authAPI} from '../../api/endpoints';
import Icon from 'react-native-vector-icons/MaterialIcons';
import {useDispatch, useSelector} from 'react-redux';
import {logout} from '../../store/authSlice';
import Button from '../../components/ui/Button';
import Input from '../../components/ui/Input';

const ProfileScreen = () => {
  const dispatch = useDispatch();
  const user = useSelector((state: any) => state.auth.user);
  const [editMode, setEditMode] = useState(false);
  const [formData, setFormData] = useState({
    full_name: '',
    phone: '',
    address: '',
  });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (user) {
      setFormData({
        full_name: user.full_name,
        phone: user.phone,
        address: user.address || '',
      });
    }
  }, [user]);

  const handleLogout = () => {
    dispatch(logout());
  };

  const handleEditProfile = () => {
    setEditMode(true);
  };

  const handleCancelEdit = () => {
    setEditMode(false);
    // Reset form to original user data
    if (user) {
      setFormData({
        full_name: user.full_name,
        phone: user.phone,
        address: user.address || '',
      });
    }
  };

  const handleSaveProfile = async () => {
    if (!formData.full_name || !formData.phone) {
      Alert.alert('Error', 'Please fill all required fields');
      return;
    }

    try {
      setLoading(true);
      await authAPI.updateProfile(formData);
      // Update user in redux store
      dispatch(setUser({...user, ...formData}));
      setEditMode(false);
      Alert.alert('Success', 'Profile updated successfully');
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to update profile. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const handleChangePassword = () => {
    // Navigate to change password screen
    navigation.navigate('ChangePassword');
  };

  if (!user) {
    return (
      <View style={[globalStyles.container, styles.loadingContainer]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <ScrollView style={globalStyles.container}>
      <View style={styles.profileHeader}>
        <View style={styles.avatarContainer}>
          <Image
            source={{uri: 'https://i.pravatar.cc/150?img=3'}}
            style={styles.avatar}
          />
          <TouchableOpacity style={styles.editAvatarButton}>
            <Icon name="edit" size={16} color={colors.white} />
          </TouchableOpacity>
        </View>
        <Text style={styles.username}>@{user.username}</Text>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Personal Information</Text>
        {editMode ? (
          <>
            <Input
              label="Full Name"
              value={formData.full_name}
              onChangeText={text => setFormData({...formData, full_name: text})}
              leftIcon={<Icon name="person" size={20} color={colors.gray} />}
            />
            <Input
              label="Phone Number"
              value={formData.phone}
              onChangeText={text => setFormData({...formData, phone: text})}
              keyboardType="phone-pad"
              leftIcon={<Icon name="phone" size={20} color={colors.gray} />}
            />
            <Input
              label="Address"
              value={formData.address}
              onChangeText={text => setFormData({...formData, address: text})}
              multiline
              leftIcon={<Icon name="home" size={20} color={colors.gray} />}
            />
          </>
        ) : (
          <>
            <View style={styles.infoRow}>
              <Icon name="person" size={20} color={colors.gray} />
              <Text style={styles.infoText}>{user.full_name}</Text>
            </View>
            <View style={styles.infoRow}>
              <Icon name="email" size={20} color={colors.gray} />
              <Text style={styles.infoText}>{user.email}</Text>
            </View>
            <View style={styles.infoRow}>
              <Icon name="phone" size={20} color={colors.gray} />
              <Text style={styles.infoText}>{user.phone}</Text>
            </View>
            {user.address && (
              <View style={styles.infoRow}>
                <Icon name="home" size={20} color={colors.gray} />
                <Text style={styles.infoText}>{user.address}</Text>
              </View>
            )}
          </>
        )}
      </View>

      {editMode ? (
        <View style={styles.buttonGroup}>
          <Button
            title="Cancel"
            onPress={handleCancelEdit}
            variant="outline"
            style={styles.button}
          />
          <Button
            title={loading ? 'Saving...' : 'Save Changes'}
            onPress={handleSaveProfile}
            disabled={loading}
            style={styles.button}
          />
        </View>
      ) : (
        <Button
          title="Edit Profile"
          onPress={handleEditProfile}
          icon={<Icon name="edit" size={20} color={colors.white} />}
        />
      )}

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Account Settings</Text>
        <TouchableOpacity
          style={styles.settingItem}
          onPress={handleChangePassword}>
          <Icon name="lock" size={20} color={colors.gray} />
          <Text style={styles.settingText}>Change Password</Text>
          <Icon name="chevron-right" size={20} color={colors.gray} />
        </TouchableOpacity>
      </View>

      <Button
        title="Logout"
        onPress={handleLogout}
        variant="outline"
        color={colors.danger}
        icon={<Icon name="logout" size={20} color={colors.danger} />}
      />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  profileHeader: {
    alignItems: 'center',
    marginBottom: 24,
  },
  avatarContainer: {
    position: 'relative',
    marginBottom: 16,
  },
  avatar: {
    width: 100,
    height: 100,
    borderRadius: 50,
  },
  editAvatarButton: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    backgroundColor: colors.primary,
    width: 32,
    height: 32,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
  },
  username: {
    fontSize: 16,
    color: colors.gray,
  },
  section: {
    backgroundColor: colors.white,
    borderRadius: 8,
    padding: 16,
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 16,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  infoText: {
    fontSize: 16,
    color: colors.dark,
    marginLeft: 12,
  },
  settingItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
  },
  settingText: {
    flex: 1,
    fontSize: 16,
    color: colors.dark,
    marginLeft: 12,
  },
  buttonGroup: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 16,
  },
  button: {
    flex: 1,
    marginHorizontal: 8,
  },
});

export default ProfileScreen;