import React, {useEffect, useState} from 'react';
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  Image,
  StyleSheet,
  ScrollView,
  Dimensions,
} from 'react-native';
import {colors, globalStyles} from '../../theme/styles';
import {boatsAPI, schedulesAPI, galleryAPI} from '../../api/endpoints';
import Icon from 'react-native-vector-icons/MaterialIcons';
import {useNavigation} from '@react-navigation/native';

const {width} = Dimensions.get('window');

const HomeScreen = () => {
  const navigation = useNavigation();
  const [featuredBoats, setFeaturedBoats] = useState<any[]>([]);
  const [upcomingSchedules, setUpcomingSchedules] = useState<any[]>([]);
  const [gallery, setGallery] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [boatsRes, schedulesRes, galleryRes] = await Promise.all([
        boatsAPI.getAllBoats(),
        schedulesAPI.getAllSchedules({
          date_from: new Date().toISOString().split('T')[0],
        }),
        galleryAPI.getFeaturedGalleryItems(),
      ]);
      setFeaturedBoats(boatsRes.data.slice(0, 3));
      setUpcomingSchedules(schedulesRes.data.slice(0, 5));
      setGallery(galleryRes.data.slice(0, 4));
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const renderBoatItem = ({item}: {item: any}) => (
    <TouchableOpacity
      style={styles.card}
      onPress={() => navigation.navigate('BoatDetail', {boatId: item.boat_id})}>
      <Image
        source={{uri: item.image_url}}
        style={styles.cardImage}
        resizeMode="cover"
      />
      <View style={styles.cardContent}>
        <Text style={styles.cardTitle}>{item.boat_name}</Text>
        <Text style={styles.cardSubtitle}>
          {item.boat_type} • {item.capacity} passengers
        </Text>
        <Text style={styles.cardPrice}>
          Rp {item.price_per_trip.toLocaleString()}/trip
        </Text>
      </View>
    </TouchableOpacity>
  );

  const renderScheduleItem = ({item}: {item: any}) => (
    <TouchableOpacity
      style={styles.scheduleCard}
      onPress={() => navigation.navigate('ScheduleDetail', {scheduleId: item.schedule_id})}>
      <View style={styles.scheduleTime}>
        <Text style={styles.scheduleTimeText}>
          {new Date(item.departure_date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
          })}
        </Text>
        <Text style={styles.scheduleTimeText}>
          {item.departure_time.substring(0, 5)}
        </Text>
      </View>
      <View style={styles.scheduleDetails}>
        <Text style={styles.scheduleRoute}>
          {item.route.departure_island.island_name} →{' '}
          {item.route.arrival_island.island_name}
        </Text>
        <Text style={styles.scheduleBoat}>{item.boat.boat_name}</Text>
        <Text style={styles.scheduleSeats}>
          {item.available_seats} seats available
        </Text>
      </View>
    </TouchableOpacity>
  );

  const renderGalleryItem = ({item}: {item: any}) => (
    <View style={styles.galleryItem}>
      <Image
        source={{uri: item.image_url}}
        style={styles.galleryImage}
        resizeMode="cover"
      />
      <Text style={styles.galleryTitle}>{item.title}</Text>
    </View>
  );

  return (
    <ScrollView style={globalStyles.container}>
      <Text style={globalStyles.title}>Featured Boats</Text>
      <FlatList
        horizontal
        data={featuredBoats}
        renderItem={renderBoatItem}
        keyExtractor={item => item.boat_id.toString()}
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.listContent}
      />

      <Text style={globalStyles.title}>Upcoming Schedules</Text>
      <FlatList
        data={upcomingSchedules}
        renderItem={renderScheduleItem}
        keyExtractor={item => item.schedule_id.toString()}
        scrollEnabled={false}
      />

      <Text style={globalStyles.title}>Gallery</Text>
      <FlatList
        horizontal
        data={gallery}
        renderItem={renderGalleryItem}
        keyExtractor={item => item.gallery_id.toString()}
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.listContent}
      />

      <TouchableOpacity
        style={[globalStyles.button, styles.exploreButton]}
        onPress={() => navigation.navigate('Schedules')}>
        <Text style={globalStyles.buttonText}>Explore All Schedules</Text>
      </TouchableOpacity>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  card: {
    width: width * 0.7,
    backgroundColor: colors.white,
    borderRadius: 8,
    marginRight: 16,
    overflow: 'hidden',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: {width: 0, height: 2},
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  cardImage: {
    width: '100%',
    height: 120,
  },
  cardContent: {
    padding: 12,
  },
  cardTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 4,
  },
  cardSubtitle: {
    fontSize: 14,
    color: colors.gray,
    marginBottom: 4,
  },
  cardPrice: {
    fontSize: 14,
    fontWeight: 'bold',
    color: colors.primary,
  },
  scheduleCard: {
    flexDirection: 'row',
    backgroundColor: colors.white,
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
    alignItems: 'center',
  },
  scheduleTime: {
    alignItems: 'center',
    marginRight: 16,
    padding: 8,
    backgroundColor: colors.light,
    borderRadius: 8,
    minWidth: 60,
  },
  scheduleTimeText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: colors.primary,
  },
  scheduleDetails: {
    flex: 1,
  },
  scheduleRoute: {
    fontSize: 16,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 4,
  },
  scheduleBoat: {
    fontSize: 14,
    color: colors.gray,
    marginBottom: 4,
  },
  scheduleSeats: {
    fontSize: 14,
    color: colors.secondary,
  },
  galleryItem: {
    width: 150,
    marginRight: 16,
  },
  galleryImage: {
    width: 150,
    height: 100,
    borderRadius: 8,
  },
  galleryTitle: {
    marginTop: 8,
    fontSize: 14,
    color: colors.dark,
    textAlign: 'center',
  },
  listContent: {
    paddingVertical: 8,
  },
  exploreButton: {
    marginTop: 16,
    marginBottom: 32,
  },
});

export default HomeScreen;