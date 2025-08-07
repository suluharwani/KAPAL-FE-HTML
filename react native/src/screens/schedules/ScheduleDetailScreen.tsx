import React, {useState, useEffect} from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  Image,
  TouchableOpacity,
} from 'react-native';
import {globalStyles, colors} from '../../theme/styles';
import {schedulesAPI} from '../../api/endpoints';
import Icon from 'react-native-vector-icons/MaterialIcons';
import {useNavigation, useRoute} from '@react-navigation/native';
import Button from '../../components/ui/Button';

const ScheduleDetailScreen = () => {
  const navigation = useNavigation();
  const route = useRoute();
  const {scheduleId} = route.params;
  const [schedule, setSchedule] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchSchedule();
  }, []);

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

  const handleBookNow = () => {
    navigation.navigate('CreateBooking', {scheduleId});
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
      <Image
        source={{uri: schedule.boat.image_url}}
        style={styles.boatImage}
        resizeMode="cover"
      />

      <View style={styles.scheduleInfo}>
        <Text style={styles.routeText}>
          {schedule.route.departure_island.island_name} →{' '}
          {schedule.route.arrival_island.island_name}
        </Text>
        <Text style={styles.dateText}>
          {new Date(schedule.departure_date).toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric',
          })}{' '}
          • {schedule.departure_time.substring(0, 5)}
        </Text>
        <Text style={styles.durationText}>
          Estimated duration: {schedule.route.estimated_duration}
        </Text>

        <View style={styles.detailRow}>
          <Icon name="directions-boat" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Boat</Text>
            <Text style={styles.detailValue}>{schedule.boat.boat_name}</Text>
          </View>
        </View>

        <View style={styles.detailRow}>
          <Icon name="people" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Available Seats</Text>
            <Text style={styles.detailValue}>{schedule.available_seats}</Text>
          </View>
        </View>

        <View style={styles.detailRow}>
          <Icon name="attach-money" size={24} color={colors.primary} />
          <View style={styles.detailContent}>
            <Text style={styles.detailLabel}>Price per trip</Text>
            <Text style={styles.detailValue}>
              Rp {schedule.boat.price_per_trip.toLocaleString()}
            </Text>
          </View>
        </View>

        <View style={styles.boatDescription}>
          <Text style={styles.sectionTitle}>Boat Description</Text>
          <Text style={styles.descriptionText}>
            {schedule.boat.description || 'No description available'}
          </Text>
        </View>

        <View style={styles.boatFacilities}>
          <Text style={styles.sectionTitle}>Facilities</Text>
          <Text style={styles.descriptionText}>
            {schedule.boat.facilities || 'No facilities listed'}
          </Text>
        </View>
      </View>

      <View style={styles.footer}>
        <View style={styles.priceContainer}>
          <Text style={styles.priceLabel}>Total Price:</Text>
          <Text style={styles.priceValue}>
            Rp {schedule.boat.price_per_trip.toLocaleString()}
          </Text>
        </View>
        <Button
          title="Book Now"
          onPress={handleBookNow}
          disabled={schedule.available_seats <= 0 || schedule.status !== 'available'}
        />
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  boatImage: {
    width: '100%',
    height: 200,
  },
  scheduleInfo: {
    padding: 16,
  },
  routeText: {
    fontSize: 22,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 8,
  },
  dateText: {
    fontSize: 16,
    color: colors.gray,
    marginBottom: 16,
  },
  durationText: {
    fontSize: 16,
    color: colors.primary,
    marginBottom: 24,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  detailContent: {
    marginLeft: 16,
  },
  detailLabel: {
    fontSize: 14,
    color: colors.gray,
  },
  detailValue: {
    fontSize: 16,
    color: colors.dark,
    fontWeight: '500',
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 8,
  },
  descriptionText: {
    fontSize: 14,
    color: colors.gray,
    lineHeight: 22,
  },
  boatDescription: {
    marginTop: 24,
    marginBottom: 16,
  },
  boatFacilities: {
    marginBottom: 24,
  },
  footer: {
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: colors.light,
  },
  priceContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  priceLabel: {
    fontSize: 16,
    color: colors.dark,
  },
  priceValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: colors.primary,
  },
});

export default ScheduleDetailScreen;