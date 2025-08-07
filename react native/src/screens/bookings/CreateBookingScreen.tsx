import React, {useState, useEffect} from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  Alert,
} from 'react-native';
import {globalStyles, colors} from '../../theme/styles';
import {schedulesAPI, bookingsAPI} from '../../api/endpoints';
import Icon from 'react-native-vector-icons/MaterialIcons';
import {useNavigation, useRoute} from '@react-navigation/native';
import Button from '../../components/ui/Button';

const CreateBookingScreen = () => {
  const navigation = useNavigation();
  const route = useRoute();
  const {scheduleId} = route.params;
  const [schedule, setSchedule] = useState<any>(null);
  const [passengerCount, setPassengerCount] = useState(1);
  const [passengers, setPassengers] = useState<any[]>([]);
  const [notes, setNotes] = useState('');
  const [paymentMethod, setPaymentMethod] = useState<'transfer' | 'cash'>(
    'transfer',
  );
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    fetchSchedule();
  }, []);

  useEffect(() => {
    // Initialize passengers array based on passengerCount
    const newPassengers = [];
    for (let i = 0; i < passengerCount; i++) {
      if (passengers[i]) {
        newPassengers.push(passengers[i]);
      } else {
        newPassengers.push({
          full_name: '',
          identity_number: '',
          phone: '',
          age: '',
        });
      }
    }
    setPassengers(newPassengers);
  }, [passengerCount]);

  const fetchSchedule = async () => {
    try {
      setLoading(true);
      const response = await schedulesAPI.getScheduleById(scheduleId);
      setSchedule(response.data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const handlePassengerChange = (index: number, field: string, value: string) => {
    const newPassengers = [...passengers];
    newPassengers[index] = {
      ...newPassengers[index],
      [field]: value,
    };
    setPassengers(newPassengers);
  };

  const validatePassengers = () => {
    for (let i = 0; i < passengers.length; i++) {
      if (!passengers[i].full_name) {
        Alert.alert('Error', `Please enter full name for passenger ${i + 1}`);
        return false;
      }
    }
    return true;
  };

  const handleSubmit = async () => {
    if (!validatePassengers()) return;

    try {
      setSubmitting(true);
      const response = await bookingsAPI.createBooking({
        schedule_id: scheduleId,
        passenger_count: passengerCount,
        passengers,
        payment_method: paymentMethod,
        notes,
      });
      navigation.navigate('BookingDetail', {
        bookingId: response.data.booking_id,
      });
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to create booking. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading || !schedule) {
    return (
      <View style={[globalStyles.container, styles.loadingContainer]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <ScrollView style={globalStyles.container}>
      <View style={styles.scheduleSummary}>
        <Text style={styles.summaryRoute}>
          {schedule.route.departure_island.island_name} →{' '}
          {schedule.route.arrival_island.island_name}
        </Text>
        <Text style={styles.summaryDate}>
          {new Date(schedule.departure_date).toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
          })}{' '}
          • {schedule.departure_time.substring(0, 5)}
        </Text>
        <Text style={styles.summaryBoat}>{schedule.boat.boat_name}</Text>
        <Text style={styles.summaryPrice}>
          Rp {schedule.boat.price_per_trip.toLocaleString()} per trip
        </Text>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Passenger Count</Text>
        <View style={styles.passengerCountContainer}>
          <TouchableOpacity
            style={styles.countButton}
            onPress={() =>
              setPassengerCount(prev => (prev > 1 ? prev - 1 : prev))
            }>
            <Icon name="remove" size={24} color={colors.primary} />
          </TouchableOpacity>
          <Text style={styles.passengerCount}>{passengerCount}</Text>
          <TouchableOpacity
            style={styles.countButton}
            onPress={() =>
              setPassengerCount(prev =>
                prev < schedule.available_seats ? prev + 1 : prev,
              )
            }>
            <Icon name="add" size={24} color={colors.primary} />
          </TouchableOpacity>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Passenger Details</Text>
        {passengers.map((passenger, index) => (
          <View key={index} style={styles.passengerCard}>
            <Text style={styles.passengerNumber}>Passenger {index + 1}</Text>
            <TextInput
              style={styles.input}
              placeholder="Full Name *"
              value={passenger.full_name}
              onChangeText={text =>
                handlePassengerChange(index, 'full_name', text)
              }
            />
            <TextInput
              style={styles.input}
              placeholder="Identity Number (optional)"
              value={passenger.identity_number}
              onChangeText={text =>
                handlePassengerChange(index, 'identity_number', text)
              }
              keyboardType="numeric"
            />
            <TextInput
              style={styles.input}
              placeholder="Phone Number (optional)"
              value={passenger.phone}
              onChangeText={text => handlePassengerChange(index, 'phone', text)}
              keyboardType="phone-pad"
            />
            <TextInput
              style={styles.input}
              placeholder="Age (optional)"
              value={passenger.age}
              onChangeText={text => handlePassengerChange(index, 'age', text)}
              keyboardType="numeric"
            />
          </View>
        ))}
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Payment Method</Text>
        <View style={styles.paymentMethodContainer}>
          <TouchableOpacity
            style={[
              styles.paymentMethodButton,
              paymentMethod === 'transfer' && styles.paymentMethodSelected,
            ]}
            onPress={() => setPaymentMethod('transfer')}>
            <Icon
              name="account-balance"
              size={24}
              color={paymentMethod === 'transfer' ? colors.white : colors.primary}
            />
            <Text
              style={[
                styles.paymentMethodText,
                paymentMethod === 'transfer' && styles.paymentMethodTextSelected,
              ]}>
              Bank Transfer
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[
              styles.paymentMethodButton,
              paymentMethod === 'cash' && styles.paymentMethodSelected,
            ]}
            onPress={() => setPaymentMethod('cash')}>
            <Icon
              name="attach-money"
              size={24}
              color={paymentMethod === 'cash' ? colors.white : colors.primary}
            />
            <Text
              style={[
                styles.paymentMethodText,
                paymentMethod === 'cash' && styles.paymentMethodTextSelected,
              ]}>
              Cash
            </Text>
          </TouchableOpacity>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Additional Notes</Text>
        <TextInput
          style={[styles.input, styles.notesInput]}
          placeholder="Any special requests or notes"
          value={notes}
          onChangeText={setNotes}
          multiline
          numberOfLines={3}
        />
      </View>

      <View style={styles.totalPriceContainer}>
        <Text style={styles.totalPriceLabel}>Total Price:</Text>
        <Text style={styles.totalPrice}>
          Rp {(schedule.boat.price_per_trip * passengerCount).toLocaleString()}
        </Text>
      </View>

      <Button
        title={submitting ? 'Processing...' : 'Confirm Booking'}
        onPress={handleSubmit}
        disabled={submitting}
      />
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  scheduleSummary: {
    backgroundColor: colors.white,
    borderRadius: 8,
    padding: 16,
    marginBottom: 16,
  },
  summaryRoute: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 8,
  },
  summaryDate: {
    fontSize: 16,
    color: colors.gray,
    marginBottom: 8,
  },
  summaryBoat: {
    fontSize: 16,
    color: colors.primary,
    marginBottom: 8,
  },
  summaryPrice: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.dark,
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
  passengerCountContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    width: 150,
    alignSelf: 'center',
  },
  countButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  passengerCount: {
    fontSize: 24,
    fontWeight: 'bold',
    color: colors.dark,
  },
  passengerCard: {
    backgroundColor: colors.light,
    borderRadius: 8,
    padding: 16,
    marginBottom: 16,
  },
  passengerNumber: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 12,
  },
  input: {
    height: 48,
    borderWidth: 1,
    borderColor: colors.gray,
    borderRadius: 8,
    paddingHorizontal: 16,
    marginBottom: 12,
    backgroundColor: colors.white,
  },
  notesInput: {
    height: 100,
    textAlignVertical: 'top',
    paddingTop: 12,
  },
  paymentMethodContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  paymentMethodButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: colors.primary,
    marginHorizontal: 4,
  },
  paymentMethodSelected: {
    backgroundColor: colors.primary,
  },
  paymentMethodText: {
    marginLeft: 8,
    color: colors.primary,
    fontWeight: 'bold',
  },
  paymentMethodTextSelected: {
    color: colors.white,
  },
  totalPriceContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 24,
    paddingHorizontal: 16,
  },
  totalPriceLabel: {
    fontSize: 18,
    color: colors.dark,
  },
  totalPrice: {
    fontSize: 20,
    fontWeight: 'bold',
    color: colors.primary,
  },
});

export default CreateBookingScreen;