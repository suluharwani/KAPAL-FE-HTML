import React from 'react';
import {createBottomTabNavigator} from '@react-navigation/bottom-tabs';
import {createNativeStackNavigator} from '@react-navigation/native-stack';
import Icon from 'react-native-vector-icons/MaterialIcons';
import HomeScreen from '../screens/home/HomeScreen';
import BookingsScreen from '../screens/bookings/BookingsScreen';
import ProfileScreen from '../screens/profile/ProfileScreen';
import {colors} from '../theme/colors';
import BoatDetailScreen from '../screens/boats/BoatDetailScreen';
import ScheduleDetailScreen from '../screens/schedules/ScheduleDetailScreen';
import CreateBookingScreen from '../screens/bookings/CreateBookingScreen';
import BookingDetailScreen from '../screens/bookings/BookingDetailScreen';
import PaymentScreen from '../screens/payments/PaymentScreen';

const Tab = createBottomTabNavigator();
const HomeStack = createNativeStackNavigator();
const BookingsStack = createNativeStackNavigator();

const HomeStackNavigator = () => {
  return (
    <HomeStack.Navigator>
      <HomeStack.Screen
        name="HomeMain"
        component={HomeScreen}
        options={{headerShown: false}}
      />
      <HomeStack.Screen
        name="BoatDetail"
        component={BoatDetailScreen}
        options={{title: 'Boat Details'}}
      />
      <HomeStack.Screen
        name="ScheduleDetail"
        component={ScheduleDetailScreen}
        options={{title: 'Schedule Details'}}
      />
      <HomeStack.Screen
        name="CreateBooking"
        component={CreateBookingScreen}
        options={{title: 'Create Booking'}}
      />
    </HomeStack.Navigator>
  );
};

const BookingsStackNavigator = () => {
  return (
    <BookingsStack.Navigator>
      <BookingsStack.Screen
        name="BookingsMain"
        component={BookingsScreen}
        options={{headerShown: false}}
      />
      <BookingsStack.Screen
        name="BookingDetail"
        component={BookingDetailScreen}
        options={{title: 'Booking Details'}}
      />
      <BookingsStack.Screen
        name="Payment"
        component={PaymentScreen}
        options={{title: 'Make Payment'}}
      />
    </BookingsStack.Navigator>
  );
};

const MainNavigator = () => {
  return (
    <Tab.Navigator
      screenOptions={({route}) => ({
        tabBarIcon: ({focused, color, size}) => {
          let iconName;

          if (route.name === 'Home') {
            iconName = focused ? 'home' : 'home';
          } else if (route.name === 'Bookings') {
            iconName = focused ? 'list-alt' : 'list';
          } else if (route.name === 'Profile') {
            iconName = focused ? 'person' : 'person-outline';
          }

          return <Icon name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: colors.primary,
        tabBarInactiveTintColor: colors.gray,
        tabBarStyle: {
          paddingBottom: 5,
          height: 60,
        },
        tabBarLabelStyle: {
          fontSize: 12,
          marginBottom: 5,
        },
        headerShown: false,
      })}>
      <Tab.Screen name="Home" component={HomeStackNavigator} />
      <Tab.Screen name="Bookings" component={BookingsStackNavigator} />
      <Tab.Screen name="Profile" component={ProfileScreen} />
    </Tab.Navigator>
  );
};

export default MainNavigator;