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
import {bookingsAPI, paymentsAPI} from '../../api/endpoints';
import Icon from 'react-native-vector-icons/MaterialIcons';
import {useNavigation, useRoute} from '@react-navigation/native';
import Button from '../../components/ui/Button';
import ImagePicker from 'react-native-image-picker';

const PaymentScreen = () => {
  const navigation = useNavigation();
  const route = useRoute();
  const {bookingId} = route.params;
  const [booking, setBooking] = useState<any>(null);
  const [paymentMethod, setPaymentMethod] = useState<'transfer' | 'cash'>(
    'transfer',
  );
  const [bankName, setBankName] = useState('');
  const [accountNumber, setAccountNumber] = useState('');
  const [receiptImage, setReceiptImage] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

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

  const handleSelectImage = () => {
    const options = {
      title: 'Select Receipt',
      storageOptions: {
        skipBackup: true,
        path: 'images',
      },
    };

    ImagePicker.launchImageLibrary(options, response => {
      if (response.didCancel) {
        console.log('User cancelled image picker');
      } else if (response.error) {
        console.log('ImagePicker Error: ', response.error);
      } else if (response.uri) {
        setReceiptImage(response);
      }
    });
  };

  const handleSubmit = async () => {
    if (paymentMethod === 'transfer' && !receiptImage) {
      Alert.alert('Error', 'Please upload payment receipt');
      return;
    }

    try {
      setSubmitting(true);
      const formData = new FormData();
      formData.append('booking_id', bookingId);
      formData.append('amount', booking.total_price);
      formData.append('payment_method', paymentMethod);
      
      if (paymentMethod === 'transfer') {
        formData.append('bank_name', bankName);
        formData.append('account_number', accountNumber);
        formData.append('receipt_image', {
          uri: receiptImage.uri,
          type: receiptImage.type,
          name: receiptImage.fileName || 'receipt.jpg',
        });
      }

      await paymentsAPI.createPayment(formData);
      Alert.alert('Success', 'Payment submitted successfully', [
        {
          text: 'OK',
          onPress: () => navigation.navigate('BookingDetail', {bookingId}),
        },
      ]);
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Failed to submit payment. Please try again.');
    } finally {
      setSubmitting(false);
    }
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
      <View style={styles.bookingSummary}>
        <Text style={styles.summaryTitle}>Booking Summary</Text>
        <Text style={styles.summaryCode}>Code: {booking.booking_code}</Text>
        <Text style={styles.summaryRoute}>
          {booking.schedule.route.departure_island.island_name} →{' '}
          {booking.schedule.route.arrival_island.island_name}
        </Text>
        <Text style={styles.summaryDate}>
          {new Date(booking.schedule.departure_date).toLocaleDateString(
            'en-US',
            {
              weekday: 'long',
              month: 'long',
              day: 'numeric',
            },
          )}{' '}
          • {booking.schedule.departure_time.substring(0, 5)}
        </Text>
        <Text style={styles.summaryPassengers}>
          {booking.passenger_count} passengers
        </Text>
        <Text style={styles.summaryTotal}>
          Total: Rp {booking.total_price.toLocaleString()}
        </Text>
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

      {paymentMethod === 'transfer' && (
        <>
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Transfer Details</Text>
            <TextInput
              style={styles.input}
              placeholder="Bank Name"
              value={bankName}
              onChangeText={setBankName}
            />
            <TextInput
              style={styles.input}
              placeholder="Account Number"
              value={accountNumber}
              onChangeText={setAccountNumber}
              keyboardType="numeric"
            />
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Payment Receipt</Text>
            <TouchableOpacity
              style={styles.uploadButton}
              onPress={handleSelectImage}>
              <Icon
                name="cloud-upload"
                size={24}
                color={colors.primary}
                style={styles.uploadIcon}
              />
              <Text style={styles.uploadText}>
                {receiptImage ? 'Change Receipt' : 'Upload Receipt'}
              </Text>
            </TouchableOpacity>
            {receiptImage && (
              <Image
                source={{uri: receiptImage.uri}}
                style={styles.receiptImage}
                resizeMode="contain"
              />
            )}
          </View>
        </>
      )}

      {paymentMethod === 'cash' && (
        <View style={styles.section}>
          <Text style={styles.noteText}>
            Please pay the amount in cash when boarding the boat. Show your
            booking code to the boat operator.
          </Text>
        </View>
      )}

      <Button
        title={submitting ? 'Processing...' : 'Submit Payment'}
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
  bookingSummary: {
    backgroundColor: colors.white,
    borderRadius: 8,
    padding: 16,
    marginBottom: 16,
  },
  summaryTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: colors.dark,
    marginBottom: 12,
  },
  summaryCode: {
    fontSize: 16,
    color: colors.primary,
    marginBottom: 8,
  },
  summaryRoute: {
    fontSize: 16,
    color: colors.dark,
    marginBottom: 8,
  },
  summaryDate: {
    fontSize: 14,
    color: colors.gray,
    marginBottom: 8,
  },
  summaryPassengers: {
    fontSize: 14,
    color: colors.gray,
    marginBottom: 8,
  },
  summaryTotal: {
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
  input: {
    height: 48,
    borderWidth: 1,
    borderColor: colors.gray,
    borderRadius: 8,
    paddingHorizontal: 16,
    marginBottom: 12,
    backgroundColor: colors.white,
  },
  uploadButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 16,
    borderWidth: 1,
    borderColor: colors.primary,
    borderRadius: 8,
    borderStyle: 'dashed',
  },
  uploadIcon: {
    marginRight: 8,
  },
  uploadText: {
    color: colors.primary,
    fontWeight: 'bold',
  },
  receiptImage: {
    width: '100%',
    height: 200,
    marginTop: 16,
    borderRadius: 8,
  },
  noteText: {
    fontSize: 14,
    color: colors.gray,
    lineHeight: 22,
  },
});

export default PaymentScreen;