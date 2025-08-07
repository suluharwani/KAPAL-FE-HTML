import React, {useEffect, useState} from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
} from 'react-native';
import {globalStyles, colors} from '../../theme/styles';
import {bookingsAPI} from '../../api/endpoints';
import Icon from 'react-native-vector-icons/MaterialIcons';
import {useNavigation} from '@react-navigation/native';

const BookingsScreen = () => {
  const navigation = useNavigation();
  const [bookings, setBookings] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchBookings();
  }, []);

  const fetchBookings = async () => {
    try {
      setLoading(true);
      const response = await bookingsAPI.getAllBookings();
      setBookings(response.data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const renderBookingItem = ({item}: {item: any}) => (
    <TouchableOpacity
      style={[globalStyles.card, styles.bookingCard]}
      onPress={() =>
        navigation.navigate('BookingDetail', {bookingId: item.booking_id})
      }>
      <View style={styles.bookingHeader}>
        <Text style={styles.bookingCode}>{item.booking_code}</Text>
        <View
          style={[
            styles.statusBadge,
            {
              backgroundColor:
                item.booking_status === 'confirmed' ||
                item.booking_status === 'paid' ||
                item.booking_status === 'completed'
                  ? colors.secondary
                  : item.booking_status === 'canceled'
                  ? colors.danger
                  : colors.warning,
            },
          ]}>
          <Text style={styles.statusText}>{item.booking_status}</Text>
        </View>
      </View>
      <Text style={styles.bookingRoute}>
        {item.schedule.route.departure_island.island_name} →{' '}
        {item.schedule.route.arrival_island.island_name}
      </Text>
      <Text style={styles.bookingDate}>
        {new Date(item.schedule.departure_date).toLocaleDateString('en-US', {
          weekday: 'short',
          month: 'short',
          day: 'numeric',
          year: 'numeric',
        })}{' '}
        • {item.schedule.departure_time.substring(0, 5)}
      </Text>
      <View style={styles.bookingFooter}>
        <Text style={styles.bookingPassengers}>
          <Icon name="people" size={16} /> {item.passenger_count} passengers
        </Text>
        <Text style={styles.bookingPrice}>
          Rp {item.total_price.toLocaleString()}
        </Text>
      </View>
    </TouchableOpacity>
  );

  if (loading) {
    return (
      <View style={[globalStyles.container, styles.loadingContainer]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  if (bookings.length === 0) {
    return (
      <View style={[globalStyles.container, styles.emptyContainer]}>
        <Icon name="receipt" size={50} color={colors.gray} />
        <Text style={styles.emptyText}>No bookings yet</Text>
        <Text style={styles.emptySubtext}>
          Book your first trip to see it here
        </Text>
      </View>
    );
  }

  return (
    <View style={globalStyles.container}>
      <FlatList
        data={bookings}
        renderItem={renderBookingItem}
        keyExtractor={item => item.booking_id.toString()}
        contentContainerStyle={styles.listContent}
        refreshing={loading}
        onRefresh={fetchBookings}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  bookingCard: {
    marginBottom: 16,
  },
  bookingHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  bookingCode: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.dark,
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    color: colors.white,
    fontWeight: 'bold',
  },
  bookingRoute: {
    fontSize: 16,
    color: colors.dark,
    marginBottom: 4,
  },
  bookingDate: {
    fontSize: 14,
    color: colors.gray,
    marginBottom: 8,
  },
  bookingFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  bookingPassengers: {
    fontSize: 14,
    color: colors.gray,
  },
  bookingPrice: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.primary,
  },
  listContent: {
    paddingVertical: 8,
  },
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyContainer: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyText: {
    fontSize: 18,
    color: colors.dark,
    marginTop: 16,
  },
  emptySubtext: {
    fontSize: 14,
    color: colors.gray,
    marginTop: 8,
  },
});

export default BookingsScreen;