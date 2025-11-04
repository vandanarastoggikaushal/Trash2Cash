import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  Alert,
  Switch,
  Platform,
} from 'react-native';
import DateTimePicker from '@react-native-community/datetimepicker';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { submitPickupRequest } from '../services/api';
import { APP_CONFIG } from '../config/api';
import { colors } from '../theme';

export default function SchedulePickupScreen() {
  const [formData, setFormData] = useState({
    fullName: '',
    email: '',
    phone: '',
    street: '',
    suburb: '',
    city: APP_CONFIG.CITY,
    postcode: '',
    accessNotes: '',
    pickupType: 'cans',
    cansEstimate: '',
    appliances: {},
    preferredDate: '',
    preferredWindow: '',
    payoutMethod: 'bank',
    bankName: '',
    bankAccount: '',
    childName: '',
    childBankAccount: '',
    kiwisaverProvider: '',
    kiwisaverMemberId: '',
    marketingOptIn: false,
    itemsAreClean: false,
    acceptedTerms: false,
  });

  const [loading, setLoading] = useState(false);
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [selectedDate, setSelectedDate] = useState(new Date());

  const updateField = (field, value) => {
    setFormData({ ...formData, [field]: value });
  };

  const updateAppliance = (slug, value) => {
    const appliances = { ...formData.appliances, [slug]: parseInt(value) || 0 };
    setFormData({ ...formData, appliances });
  };

  const calculateCansReward = () => {
    const cans = parseInt(formData.cansEstimate) || 0;
    return Math.floor(cans / 100);
  };

  const calculateApplianceReward = () => {
    return Object.entries(formData.appliances).reduce((sum, [slug, qty]) => {
      return sum + (APP_CONFIG.APPLIANCE_CREDITS[slug]?.credit || 0) * qty;
    }, 0);
  };

  const handleSubmit = async () => {
    if (!formData.fullName || !formData.email || !formData.phone) {
      Alert.alert('Error', 'Please fill in all required fields');
      return;
    }

    if (!formData.itemsAreClean || !formData.acceptedTerms) {
      Alert.alert('Error', 'Please confirm items are clean and accept terms');
      return;
    }

    setLoading(true);
    try {
      const result = await submitPickupRequest(formData);
      if (result.ok) {
        Alert.alert(
          'Success!',
          `Your pickup request has been submitted.\nReference ID: ${result.id}`,
          [{ text: 'OK', onPress: () => {
            // Reset form
            setFormData({
              ...formData,
              fullName: '',
              email: '',
              phone: '',
              street: '',
              suburb: '',
              postcode: '',
              accessNotes: '',
              cansEstimate: '',
              appliances: {},
              preferredDate: '',
              preferredWindow: '',
            });
          }}]
        );
      } else {
        Alert.alert('Error', result.error || 'Failed to submit request. Please try again.');
      }
    } catch (error) {
      console.error('Pickup submission error:', error);
      Alert.alert('Error', error.message || 'Failed to submit request. Please check your connection and try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.section}>
        <Text style={styles.title}>Schedule a Pickup</Text>

        {/* Person Details */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>👤 Person Details</Text>
          <TextInput
            style={styles.input}
            placeholder="Full name *"
            value={formData.fullName}
            onChangeText={(text) => updateField('fullName', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Email *"
            keyboardType="email-address"
            value={formData.email}
            onChangeText={(text) => updateField('email', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Phone *"
            keyboardType="phone-pad"
            value={formData.phone}
            onChangeText={(text) => updateField('phone', text)}
          />
          <View style={styles.switchRow}>
            <Text>I'd like to receive updates</Text>
            <Switch
              value={formData.marketingOptIn}
              onValueChange={(value) => updateField('marketingOptIn', value)}
              trackColor={{ false: '#d1d5db', true: colors.brand }}
            />
          </View>
        </View>

        {/* Address */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>📍 Address</Text>
          <TextInput
            style={styles.input}
            placeholder="Street *"
            value={formData.street}
            onChangeText={(text) => updateField('street', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Suburb *"
            value={formData.suburb}
            onChangeText={(text) => updateField('suburb', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="City *"
            value={formData.city}
            onChangeText={(text) => updateField('city', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Postcode *"
            keyboardType="numeric"
            maxLength={4}
            value={formData.postcode}
            onChangeText={(text) => updateField('postcode', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Access notes (optional)"
            value={formData.accessNotes}
            onChangeText={(text) => updateField('accessNotes', text)}
            multiline
          />
        </View>

        {/* Pickup Type */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>♻️ Pickup Type</Text>
          <View style={styles.radioGroup}>
            <TouchableOpacity
              style={[
                styles.radioButton,
                formData.pickupType === 'cans' && styles.radioButtonActive,
              ]}
              onPress={() => updateField('pickupType', 'cans')}
            >
              <Text style={styles.radioButtonText}>🥤 Cans Only</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[
                styles.radioButton,
                formData.pickupType === 'appliances' && styles.radioButtonActive,
              ]}
              onPress={() => updateField('pickupType', 'appliances')}
            >
              <Text style={styles.radioButtonText}>🔧 Appliances Only</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[
                styles.radioButton,
                formData.pickupType === 'both' && styles.radioButtonActive,
              ]}
              onPress={() => updateField('pickupType', 'both')}
            >
              <Text style={styles.radioButtonText}>♻️ Both</Text>
            </TouchableOpacity>
          </View>

          {/* Cans Input */}
          {(formData.pickupType === 'cans' || formData.pickupType === 'both') && (
            <View style={styles.cansSection}>
              <Text style={styles.label}>Number of Cans (estimate)</Text>
              <TextInput
                style={styles.input}
                placeholder="Enter number of cans"
                keyboardType="numeric"
                value={formData.cansEstimate}
                onChangeText={(text) => updateField('cansEstimate', text)}
              />
              {formData.cansEstimate && (
                <Text style={styles.rewardText}>
                  Estimated reward: ${calculateCansReward()}
                </Text>
              )}
            </View>
          )}

          {/* Appliances Input */}
          {(formData.pickupType === 'appliances' || formData.pickupType === 'both') && (
            <View style={styles.appliancesSection}>
              <Text style={styles.label}>Appliances (quantity per type)</Text>
              {Object.entries(APP_CONFIG.APPLIANCE_CREDITS).map(([slug, data]) => (
                <View key={slug} style={styles.applianceRow}>
                  <Text style={styles.applianceLabel}>{data.label}</Text>
                  <TextInput
                    style={styles.applianceInput}
                    keyboardType="numeric"
                    value={(formData.appliances[slug] || 0).toString()}
                    onChangeText={(value) => updateAppliance(slug, value)}
                    placeholder="0"
                  />
                </View>
              ))}
              {Object.values(formData.appliances).some(qty => qty > 0) && (
                <Text style={styles.rewardText}>
                  Total appliance credit: ${calculateApplianceReward()}
                </Text>
              )}
            </View>
          )}

          {/* Preferred Date/Time */}
          <View>
            <Text style={styles.label}>📅 Preferred date</Text>
            <TouchableOpacity
              style={styles.input}
              onPress={() => setShowDatePicker(true)}
            >
              <Text style={formData.preferredDate ? styles.inputText : styles.placeholderText}>
                {formData.preferredDate || 'Select date'}
              </Text>
            </TouchableOpacity>
            {showDatePicker && (
              <DateTimePicker
                value={selectedDate}
                mode="date"
                display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                onChange={(event, date) => {
                  if (Platform.OS === 'android') {
                    setShowDatePicker(false);
                  }
                  if (date && event.type !== 'dismissed') {
                    setSelectedDate(date);
                    const formattedDate = date.toISOString().split('T')[0];
                    updateField('preferredDate', formattedDate);
                  }
                  if (Platform.OS === 'android' && event.type === 'dismissed') {
                    setShowDatePicker(false);
                  }
                }}
                minimumDate={new Date()}
              />
            )}
            {Platform.OS === 'ios' && showDatePicker && (
              <View style={styles.datePickerActions}>
                <TouchableOpacity
                  style={styles.datePickerButton}
                  onPress={() => setShowDatePicker(false)}
                >
                  <Text style={styles.datePickerButtonText}>Done</Text>
                </TouchableOpacity>
              </View>
            )}
          </View>
          
          <View>
            <Text style={styles.label}>🕐 Preferred time window</Text>
            <View style={styles.timeWindowContainer}>
              {['Morning', 'Afternoon', 'Evening'].map((window) => (
                <TouchableOpacity
                  key={window}
                  style={[
                    styles.timeWindowButton,
                    formData.preferredWindow === window && styles.timeWindowButtonActive,
                  ]}
                  onPress={() => updateField('preferredWindow', window)}
                >
                  <Text
                    style={[
                      styles.timeWindowButtonText,
                      formData.preferredWindow === window && styles.timeWindowButtonTextActive,
                    ]}
                  >
                    {window}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>
        </View>

        {/* Payout Preference */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>💰 Payout Preference</Text>
          <View style={styles.radioGroup}>
            <TouchableOpacity
              style={[
                styles.radioButton,
                formData.payoutMethod === 'bank' && styles.radioButtonActive,
              ]}
              onPress={() => updateField('payoutMethod', 'bank')}
            >
              <Text style={styles.radioButtonText}>Bank</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[
                styles.radioButton,
                formData.payoutMethod === 'child_account' && styles.radioButtonActive,
              ]}
              onPress={() => updateField('payoutMethod', 'child_account')}
            >
              <Text style={styles.radioButtonText}>Child Account</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[
                styles.radioButton,
                formData.payoutMethod === 'kiwisaver' && styles.radioButtonActive,
              ]}
              onPress={() => updateField('payoutMethod', 'kiwisaver')}
            >
              <Text style={styles.radioButtonText}>KiwiSaver</Text>
            </TouchableOpacity>
          </View>

          {formData.payoutMethod === 'bank' && (
            <>
              <TextInput
                style={styles.input}
                placeholder="Bank name"
                value={formData.bankName}
                onChangeText={(text) => updateField('bankName', text)}
              />
              <TextInput
                style={styles.input}
                placeholder="Account number"
                keyboardType="numeric"
                value={formData.bankAccount}
                onChangeText={(text) => updateField('bankAccount', text)}
              />
            </>
          )}

          {formData.payoutMethod === 'child_account' && (
            <>
              <TextInput
                style={styles.input}
                placeholder="Child name"
                value={formData.childName}
                onChangeText={(text) => updateField('childName', text)}
              />
              <TextInput
                style={styles.input}
                placeholder="Bank account (optional)"
                keyboardType="numeric"
                value={formData.childBankAccount}
                onChangeText={(text) => updateField('childBankAccount', text)}
              />
            </>
          )}

          {formData.payoutMethod === 'kiwisaver' && (
            <>
              <TextInput
                style={styles.input}
                placeholder="KiwiSaver provider"
                value={formData.kiwisaverProvider}
                onChangeText={(text) => updateField('kiwisaverProvider', text)}
              />
              <TextInput
                style={styles.input}
                placeholder="Member ID"
                value={formData.kiwisaverMemberId}
                onChangeText={(text) => updateField('kiwisaverMemberId', text)}
              />
            </>
          )}
        </View>

        {/* Confirmations */}
        <View style={styles.card}>
          <View style={styles.switchRow}>
            <Text>Items are clean and safe to handle *</Text>
            <Switch
              value={formData.itemsAreClean}
              onValueChange={(value) => updateField('itemsAreClean', value)}
              trackColor={{ false: '#d1d5db', true: colors.brand }}
            />
          </View>
          <View style={styles.switchRow}>
            <Text>I accept the terms *</Text>
            <Switch
              value={formData.acceptedTerms}
              onValueChange={(value) => updateField('acceptedTerms', value)}
              trackColor={{ false: '#d1d5db', true: colors.brand }}
            />
          </View>
        </View>

        {/* Submit Button */}
        <TouchableOpacity
          style={[styles.submitButton, loading && styles.submitButtonDisabled]}
          onPress={handleSubmit}
          disabled={loading}
        >
          <Text style={styles.submitButtonText}>
            {loading ? 'Submitting...' : 'Submit Request'}
          </Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f9fafb',
  },
  section: {
    padding: 20,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 24,
  },
  card: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    marginBottom: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 16,
  },
  input: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    marginBottom: 12,
  },
  switchRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
  },
  radioGroup: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
    marginBottom: 16,
  },
  radioButton: {
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: 8,
    borderWidth: 2,
    borderColor: '#d1d5db',
    backgroundColor: '#fff',
  },
  radioButtonActive: {
    borderColor: colors.brand,
    backgroundColor: '#ecfdf5',
  },
  radioButtonText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#374151',
  },
  cansSection: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#e5e7eb',
  },
  appliancesSection: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#e5e7eb',
  },
  label: {
    fontSize: 16,
    fontWeight: '500',
    color: '#374151',
    marginBottom: 8,
  },
  applianceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 8,
  },
  applianceLabel: {
    fontSize: 14,
    color: '#374151',
    flex: 1,
  },
  applianceInput: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 8,
    padding: 8,
    width: 60,
    textAlign: 'center',
    fontSize: 16,
  },
  rewardText: {
    fontSize: 14,
    color: colors.brand,
    fontWeight: '500',
    marginTop: 8,
  },
  inputText: {
    fontSize: 16,
    color: '#1f2937',
  },
  placeholderText: {
    fontSize: 16,
    color: '#9ca3af',
  },
  timeWindowContainer: {
    flexDirection: 'row',
    gap: 8,
    marginTop: 8,
  },
  timeWindowButton: {
    flex: 1,
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderRadius: 8,
    borderWidth: 2,
    borderColor: '#d1d5db',
    backgroundColor: '#fff',
    alignItems: 'center',
  },
  timeWindowButtonActive: {
    borderColor: colors.brand,
    backgroundColor: '#ecfdf5',
  },
  timeWindowButtonText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#374151',
  },
  timeWindowButtonTextActive: {
    color: colors.brand,
    fontWeight: 'bold',
  },
  datePickerActions: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    paddingTop: 8,
  },
  datePickerButton: {
    paddingHorizontal: 20,
    paddingVertical: 10,
    backgroundColor: colors.brand,
    borderRadius: 8,
  },
  datePickerButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  submitButton: {
    backgroundColor: colors.brand,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    marginTop: 8,
  },
  submitButtonDisabled: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
});
