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
import {bookingsAPI} from '../../api/endpoints';
import Icon from 'react-native-vector-icons/MaterialIcons';
import {useNavigation, useRoute} from '@react-navigation/native';
import Button from '../../components/ui/Button';

const BookingDetailScreen = () => {
  const navigation = useNavigation();
  const route = useRoute();
  const {bookingId} = route.params;
  const [booking, setBooking] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [canceling, setCanceling] = useState(false);

  useEffect(() => {
    fetchBooking();
  }, []);

  const fetchBooking = async () => {
    try {
      setLoading(true);
      const response = await bookingsAPI.getBookingById(bookingId);
      setBooking(response.data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const handleCancelBooking = async () => {
    Alert.alert(
      'Confirm Cancellation',
      'Are you sure you want to cancel this booking?',
      [
        {
          text: 'No',
          style: 'cancel',
        },
        {
          text: 'Yes',
          onPress: async () => {
            try {
              setCanceling(true);
              await bookingsAPI.cancelBooking(bookingId);
              Alert.alert('Success', 'Booking canceled successfully');
              navigation.goBack();
            } catch (error) {
              console.error(error);
              Alert.alert('Error', 'Failed to cancel booking. Please try again.');
            } finally {
              setCanceling(false);
            }
          },
        },
      ],
    );
  };

  const handleMakePayment = () => {
    navigation.navigate('Payment', {bookingId});
  };

  if (loading || !booking) {
    return (
      <View style={[globalStyles.container, styles.loadingContainer]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return (
    <ScrollView style={globalStyles.container}>
      <View style={styles.header}>
        <Text style={styles.bookingCode}>{booking.booking_code}</Text>
        <View
          style={[
            styles.statusBadge,
            {
              backgroundColor:
                booking.booking_status === 'confirmed' ||
                booking.booking_status === 'paid' ||
                booking.booking_status === 'completed'
                  ? colors.secondary
                  : booking.booking_status === 'canceled'
                  ? colors.danger
                  : colors.warning,
            },
          ]}>
          <Text style={styles.statusText}>{booking.booking_status}</Text>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Trip Details</Text>
        <View style={styles.detailRow}>
          <Icon name="location-on" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Route</Text>
            <Text style={styles.detailValue}>
              {booking.schedule.route.departure_island.island_name} →{' '}
              {booking.schedule.route.arrival_island.island_name}
            </Text>
          </View>
        </View>

        <View style={styles.detailRow}>
          <Icon name="date-range" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Date & Time</Text>
            <Text style={styles.detailValue}>
              {new Date(booking.schedule.departure_date).toLocaleDateString(
                'en-US',
                {
                  weekday: 'long',
                  month: 'long',
                  day: 'numeric',
                  year: 'numeric',
                },
              )}{' '}
              • {booking.schedule.departure_time.substring(0, 5)}
            </Text>
          </View>
        </View>

        <View style={styles.detailRow}>
          <Icon name="directions-boat" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Boat</Text>
            <Text style={styles.detailValue}>{booking.schedule.boat.boat_name}</Text>
          </View>
        </View>

        <View style={styles.detailRow}>
          <Icon name="schedule" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Duration</Text>
            <Text style={styles.detailValue}>
              {booking.schedule.route.estimated_duration}
            </Text>
          </View>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Passengers</Text>
        {booking.passengers.map((passenger: any, index: number) => (
          <View key={index} style={styles.passengerItem}>
            <Text style={styles.passengerName}>
              {index + 1}. {passenger.full_name}
            </Text>
            {passenger.identity_number && (
              <Text style={styles.passengerDetail}>
                ID: {passenger.identity_number}
              </Text>
            )}
            {passenger.phone && (
              <Text style={styles.passengerDetail}>
                Phone: {passenger.phone}
              </Text>
            )}
            {passenger.age && (
              <Text style={styles.passengerDetail}>Age: {passenger.age}</Text>
            )}
          </View>
        ))}
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Payment Information</Text>
        <View style={styles.detailRow}>
          <Icon name="payment" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Payment Method</Text>
            <Text style={styles.detailValue}>
              {booking.payment_method === 'transfer' ? 'Bank Transfer' : 'Cash'}
            </Text>
          </View>
        </View>

        <View style={styles.detailRow}>
          <Icon name="attach-money" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Total Price</Text>
            <Text style={styles.detailValue}>
              Rp {booking.total_price.toLocaleString()}
            </Text>
          </View>
        </View>

        <View style={styles.detailRow}>
          <Icon name="info" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Payment Status</Text>
            <Text
              style={[
                styles.detailValue,
                {
                  color:
                    booking.payment_status === 'paid'
                      ? colors.secondary
                      : booking.payment_status === 'pending'
                      ? colors.warning
                      : colors.danger,
                },
              ]}>
              {booking.payment_status}
            </Text>
          </View>
        </View>
      </View>

      {booking.notes && (
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Notes</Text>
          <Text style={styles.notesText}>{booking.notes}</Text>
        </View>
      )}

      <View style={styles.actions}>
        {booking.booking_status === 'pending' &&
          booking.payment_status !== 'paid' && (
            <>
              <Button
                title="Make Payment"
                onPress={handleMakePayment}
                style={styles.actionButton}
              />
              <Button
                title={canceling ? 'Canceling...' : 'Cancel Booking'}
                onPress={handleCancelBooking}
                variant="outline"
                color={colors.danger}
                disabled={canceling}
                style={styles.actionButton}
              />
            </>
          )}
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 24,
  },
  bookingCode: {
    fontSize: 20,
    fontWeight: 'bold',
    color: colors.dark,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  statusText: {
    fontSize: 14,
    color: colors.white,
    fontWeight: 'bold',
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
  detailRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 16,
  },
  detailContent: {
    marginLeft: 16,
    flex: 1,
  },
  detailLabel: {
    fontSize: 14,
    color: colors.gray,
    marginBottom: 4,
  },
  detailValue: {
    fontSize: 16,
    color: colors.dark,
  },
  passengerItem: {
    backgroundColor: colors.light,
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  passengerName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 4,
  },
  passengerDetail: {
    fontSize: 14,
    color: colors.gray,
    marginLeft: 16,
  },
  notesText: {
    fontSize: 14,
    color: colors.gray,
    lineHeight: 22,
  },
  actions: {
    marginTop: 16,
  },
  actionButton: {
    marginBottom: 12,
  },
});

export default BookingDetailScreen;