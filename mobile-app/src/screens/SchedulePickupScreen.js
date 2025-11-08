import React, { useContext, useEffect, useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  Alert,
  TextInput,
  Platform,
} from 'react-native';
import DateTimePicker from '@react-native-community/datetimepicker';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { submitPickupRequest } from '../services/api';
import { AuthContext } from '../context/AuthContext';
import { APP_CONFIG } from '../config/api';
import { colors } from '../theme';

const initialPickupState = {
  pickupType: 'cans',
  cansEstimate: '',
  appliances: {},
  preferredDate: '',
  preferredWindow: '',
  accessNotes: '',
  itemsAreClean: false,
  acceptedTerms: false,
};

export default function SchedulePickupScreen({ navigation }) {
  const { user, isAuthenticated, refreshProfile } = useContext(AuthContext);
  const [formData, setFormData] = useState(initialPickupState);
  const [loading, setLoading] = useState(false);
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [selectedDate, setSelectedDate] = useState(new Date());

  useEffect(() => {
    refreshProfile?.();
  }, []);

  const updateField = (field, value) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const updateAppliance = (slug, value) => {
    const quantity = parseInt(value, 10) || 0;
    setFormData((prev) => ({
      ...prev,
      appliances: { ...prev.appliances, [slug]: quantity },
    }));
  };

  const calculateCansReward = () => {
    const cans = parseInt(formData.cansEstimate, 10) || 0;
    return Math.floor(cans / 100);
  };

  const calculateApplianceReward = () => {
    return Object.entries(formData.appliances).reduce((sum, [slug, qty]) => {
      return sum + (APP_CONFIG.APPLIANCE_CREDITS[slug]?.credit || 0) * qty;
    }, 0);
  };

  const accountAddress = user?.address || {};
  const payout = user?.payout || {};

  const hasAddress =
    accountAddress.street &&
    accountAddress.suburb &&
    accountAddress.city &&
    accountAddress.postcode;

  const payoutValid = (() => {
    switch (payout.method) {
      case 'child_account':
        return !!payout.childName;
      case 'kiwisaver':
        return !!payout.kiwisaverProvider && !!payout.kiwisaverMemberId;
      default:
        return !!payout.bankName && !!payout.bankAccount;
    }
  })();

  const accountReady = isAuthenticated && hasAddress && payoutValid;

  const resetPickupForm = () => {
    setFormData(initialPickupState);
    setSelectedDate(new Date());
  };

  const handleSubmit = async () => {
    if (!accountReady) {
      Alert.alert('Update required', 'Please complete your account details before scheduling a pickup.');
      return;
    }

    if (!formData.itemsAreClean || !formData.acceptedTerms) {
      Alert.alert('Confirm pickup', 'Please confirm the items are clean and accept the terms.');
      return;
    }

    setLoading(true);
    try {
      const submission = {
        fullName: `${user.firstName || ''} ${user.lastName || ''}`.trim() || user.username || '',
        email: user.email || '',
        phone: user.phone || '',
        marketingOptIn: !!user.marketingOptIn,
        street: accountAddress.street,
        suburb: accountAddress.suburb,
        city: accountAddress.city,
        postcode: accountAddress.postcode,
        accessNotes: formData.accessNotes,
        pickupType: formData.pickupType,
        cansEstimate: formData.cansEstimate,
        appliances: formData.appliances,
        preferredDate: formData.preferredDate,
        preferredWindow: formData.preferredWindow,
        payoutMethod: payout.method || 'bank',
        bankName: payout.bankName || '',
        bankAccount: payout.bankAccount || '',
        childName: payout.childName || '',
        childBankAccount: payout.childBankAccount || '',
        kiwisaverProvider: payout.kiwisaverProvider || '',
        kiwisaverMemberId: payout.kiwisaverMemberId || '',
        itemsAreClean: formData.itemsAreClean,
        acceptedTerms: formData.acceptedTerms,
      };

      const result = await submitPickupRequest(submission);
      if (result.ok) {
        Alert.alert(
          'Success!',
          `Your pickup request has been submitted.\nReference ID: ${result.id}`,
          [{ text: 'OK', onPress: resetPickupForm }]
        );
      } else {
        Alert.alert('Error', result.error || 'Failed to submit request. Please try again.');
      }
    } catch (error) {
      console.error('[Schedule] submission error', error);
      Alert.alert('Error', error.message || 'Failed to submit request. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  if (!isAuthenticated) {
    return (
      <View style={styles.centered}>
        <Text style={styles.title}>Schedule a Pickup</Text>
        <Text style={styles.subtitle}>
          Please log in to confirm your address and payout details before scheduling a pickup.
        </Text>
        <TouchableOpacity
          style={styles.primaryButton}
          onPress={() => navigation.navigate('Login')}
        >
          <Text style={styles.primaryButtonText}>Login</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={styles.secondaryButton}
          onPress={() => navigation.navigate('Register')}
        >
          <Text style={styles.secondaryButtonText}>Create an account</Text>
        </TouchableOpacity>
      </View>
    );
  }

  if (!accountReady) {
    return (
      <View style={styles.centered}>
        <Text style={styles.title}>Schedule a Pickup</Text>
        <Text style={styles.subtitle}>
          We need an up-to-date address and payout method before we can schedule a pickup.
        </Text>
        <TouchableOpacity
          style={styles.primaryButton}
          onPress={() => navigation.navigate('Main', { screen: 'Account' })}
        >
          <Text style={styles.primaryButtonText}>Update account details</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      <View style={styles.section}>
        <Text style={styles.title}>Schedule a Pickup</Text>
        <Text style={styles.subtitle}>
          We’ll use the address and payout details saved in your account.
        </Text>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>Account summary</Text>
          <Text style={styles.summaryLabel}>Name</Text>
          <Text style={styles.summaryValue}>{`${user.firstName || ''} ${user.lastName || ''}`.trim() || user.username}</Text>
          <Text style={styles.summaryLabel}>Email</Text>
          <Text style={styles.summaryValue}>{user.email || '—'}</Text>
          <Text style={styles.summaryLabel}>Phone</Text>
          <Text style={styles.summaryValue}>{user.phone || '—'}</Text>
          <Text style={styles.summaryLabel}>Address</Text>
          <Text style={styles.summaryValue}>
            {[accountAddress.street, accountAddress.suburb, `${accountAddress.city} ${accountAddress.postcode}`]
              .filter(Boolean)
              .join('\n')}
          </Text>
          <Text style={styles.summaryLabel}>Payout method</Text>
          <Text style={styles.summaryValue}>
            {payout.method === 'child_account'
              ? `Child account (${payout.childName || 'name required'})`
              : payout.method === 'kiwisaver'
              ? `KiwiSaver (${payout.kiwisaverProvider || 'provider required'})`
              : `Bank account (${payout.bankName || 'bank'}, ${payout.bankAccount || 'account'})`}
          </Text>
          <TouchableOpacity
            style={styles.secondaryButton}
            onPress={() => navigation.navigate('Main', { screen: 'Account' })}
          >
            <Text style={styles.secondaryButtonText}>Edit account details</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>♻️ Pickup details</Text>
          <View style={styles.pillRow}>
            {['cans', 'appliances', 'both'].map((type) => (
              <TouchableOpacity
                key={type}
                style={[
                  styles.pillButton,
                  formData.pickupType === type && styles.pillButtonActive,
                ]}
                onPress={() => updateField('pickupType', type)}
              >
                <Text
                  style={[
                    styles.pillText,
                    formData.pickupType === type && styles.pillTextActive,
                  ]}
                >
                  {type === 'cans' ? '🥤 Cans' : type === 'appliances' ? '🔧 Appliances' : '♻️ Both'}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          {['cans', 'both'].includes(formData.pickupType) && (
            <View style={styles.infoBox}>
              <Text style={styles.infoTitle}>Cans estimate</Text>
              <TextInput
                style={styles.input}
                placeholder="Estimated number of cans"
                keyboardType="number-pad"
                value={formData.cansEstimate}
                onChangeText={(text) => updateField('cansEstimate', text)}
              />
              <View style={styles.rewardBox}>
                <Text style={styles.rewardLabel}>Est. reward</Text>
                <Text style={styles.rewardValue}>${calculateCansReward()}</Text>
              </View>
              <Text style={styles.helperText}>💡 $1 per 100 cans</Text>
            </View>
          )}

          {['appliances', 'both'].includes(formData.pickupType) && (
            <View style={styles.infoBox}>
              <Text style={styles.infoTitle}>Appliances</Text>
              {Object.entries(APP_CONFIG.APPLIANCE_CREDITS).map(([slug, appliance]) => (
                <View key={slug} style={styles.applianceRow}>
                  <Text style={styles.applianceLabel}>{appliance.label}</Text>
                  <TextInput
                    style={styles.applianceInput}
                    keyboardType="number-pad"
                    value={(formData.appliances[slug] || '').toString()}
                    onChangeText={(value) => updateAppliance(slug, value)}
                  />
                </View>
              ))}
              <Text style={styles.helperText}>
                Total appliance credit: ${calculateApplianceReward()}
              </Text>
            </View>
          )}

          <View style={styles.dateRow}>
            <TouchableOpacity
              style={styles.dateButton}
              onPress={() => setShowDatePicker(true)}
            >
              <MaterialCommunityIcons name="calendar" size={20} color={colors.brand} />
              <Text style={styles.dateButtonText}>
                {formData.preferredDate
                  ? `Preferred date: ${new Date(formData.preferredDate).toLocaleDateString()}`
                  : 'Select preferred date'}
              </Text>
            </TouchableOpacity>
            <View style={styles.pillRow}>
              {['Morning', 'Afternoon', 'Evening'].map((window) => (
                <TouchableOpacity
                  key={window}
                  style={[
                    styles.pillButtonSmall,
                    formData.preferredWindow === window && styles.pillButtonActive,
                  ]}
                  onPress={() => updateField('preferredWindow', window)}
                >
                  <Text
                    style={[
                      styles.pillTextSmall,
                      formData.preferredWindow === window && styles.pillTextActive,
                    ]}
                  >
                    {window}
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>Notes</Text>
          <TextInput
            style={[styles.input, styles.textArea]}
            placeholder="Gate codes, pets, special instructions..."
            multiline
            numberOfLines={4}
            value={formData.accessNotes}
            onChangeText={(text) => updateField('accessNotes', text)}
          />
        </View>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>✅ Confirm & submit</Text>
          <TouchableOpacity
            style={styles.checkboxRow}
            onPress={() => updateField('itemsAreClean', !formData.itemsAreClean)}
          >
            <Text style={styles.checkboxIcon}>{formData.itemsAreClean ? '✅' : '⬜️'}</Text>
            <Text style={styles.checkboxLabel}>Items are clean and safe to handle</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={styles.checkboxRow}
            onPress={() => updateField('acceptedTerms', !formData.acceptedTerms)}
          >
            <Text style={styles.checkboxIcon}>{formData.acceptedTerms ? '✅' : '⬜️'}</Text>
            <Text style={styles.checkboxLabel}>I accept the terms and conditions</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[
              styles.primaryButton,
              (!formData.itemsAreClean || !formData.acceptedTerms) && { opacity: 0.6 },
            ]}
            onPress={handleSubmit}
            disabled={loading || !formData.itemsAreClean || !formData.acceptedTerms}
          >
            <Text style={styles.primaryButtonText}>
              {loading ? 'Submitting...' : 'Submit pickup request'}
            </Text>
          </TouchableOpacity>
        </View>
      </View>

      {showDatePicker && (
        <DateTimePicker
          value={selectedDate}
          mode="date"
          display={Platform.OS === 'ios' ? 'inline' : 'default'}
          onChange={(event, date) => {
            setShowDatePicker(false);
            if (date) {
              setSelectedDate(date);
              updateField('preferredDate', date.toISOString());
            }
          }}
        />
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  centered: {
    flex: 1,
    padding: 24,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#f3f4f6',
  },
  section: {
    padding: 16,
  },
  title: {
    fontSize: 28,
    fontWeight: '700',
    color: colors.brand,
  },
  subtitle: {
    fontSize: 14,
    color: '#4b5563',
    marginBottom: 16,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 20,
    marginBottom: 16,
    shadowColor: '#000',
    shadowOpacity: 0.05,
    shadowRadius: 12,
    elevation: 3,
  },
  cardTitle: {
    fontSize: 17,
    fontWeight: '700',
    marginBottom: 12,
    color: '#111827',
  },
  summaryLabel: {
    fontSize: 12,
    textTransform: 'uppercase',
    color: '#6b7280',
    fontWeight: '600',
    marginTop: 8,
  },
  summaryValue: {
    fontSize: 15,
    color: '#111827',
    marginBottom: 4,
  },
  pillRow: {
    flexDirection: 'row',
    gap: 8,
    marginBottom: 12,
    flexWrap: 'wrap',
  },
  pillButton: {
    flex: 1,
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 999,
    paddingVertical: 10,
    alignItems: 'center',
    backgroundColor: '#fff',
  },
  pillButtonSmall: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 999,
    paddingVertical: 8,
    paddingHorizontal: 16,
    backgroundColor: '#fff',
  },
  pillButtonActive: {
    backgroundColor: '#dcfce7',
    borderColor: colors.brand,
  },
  pillText: {
    fontWeight: '600',
    color: '#4b5563',
  },
  pillTextSmall: {
    fontWeight: '600',
    color: '#4b5563',
  },
  pillTextActive: {
    color: colors.brand,
  },
  infoBox: {
    backgroundColor: '#f9fafb',
    borderRadius: 16,
    padding: 16,
    borderWidth: 1,
    borderColor: '#e5e7eb',
    marginBottom: 12,
  },
  infoTitle: {
    fontSize: 16,
    fontWeight: '700',
    marginBottom: 8,
  },
  applianceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#e5e7eb',
  },
  applianceLabel: {
    fontSize: 15,
    color: '#374151',
  },
  applianceValue: {
    fontWeight: '600',
    color: colors.brand,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  inputButton: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 16,
    marginBottom: 12,
    backgroundColor: '#fff',
  },
  inputButtonFlex: {
    flex: 1,
  },
  inputButtonText: {
    fontSize: 15,
    color: '#1f2937',
  },
  rewardBox: {
    minWidth: 90,
    backgroundColor: '#ecfdf5',
    borderRadius: 12,
    padding: 12,
    alignItems: 'center',
  },
  rewardLabel: {
    fontSize: 12,
    color: '#166534',
    fontWeight: '600',
  },
  rewardValue: {
    fontSize: 18,
    fontWeight: '700',
    color: '#166534',
  },
  helperText: {
    fontSize: 12,
    color: '#6b7280',
    marginTop: 4,
  },
  dateRow: {
    marginTop: 12,
  },
  dateButton: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#d1d5db',
    marginBottom: 12,
    backgroundColor: '#fff',
    gap: 8,
  },
  dateButtonText: {
    fontWeight: '600',
    color: '#1f2937',
  },
  checkboxRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
    gap: 12,
  },
  checkboxIcon: {
    fontSize: 18,
  },
  checkboxLabel: {
    fontSize: 15,
    color: '#1f2937',
  },
  primaryButton: {
    backgroundColor: colors.brand,
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 8,
  },
  primaryButtonText: {
    color: '#fff',
    fontWeight: '700',
    fontSize: 16,
  },
  secondaryButton: {
    borderWidth: 1,
    borderColor: colors.brand,
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: 'center',
    marginTop: 8,
  },
  secondaryButtonText: {
    color: colors.brand,
    fontWeight: '700',
    fontSize: 16,
  },
});
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
import { submitPickupRequest, searchAddress, getAddressDetails } from '../services/api';
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
  const [addressSearchQuery, setAddressSearchQuery] = useState('');
  const [addressSuggestions, setAddressSuggestions] = useState([]);
  const [showAddressSuggestions, setShowAddressSuggestions] = useState(false);

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
          
          {/* Address Search */}
          <View style={styles.addressSearchContainer}>
            <Text style={styles.label}>🔍 Search Address</Text>
            <TextInput
              style={styles.input}
              placeholder="Start typing your address..."
              value={addressSearchQuery}
              onChangeText={async (text) => {
                setAddressSearchQuery(text);
                if (text.length >= 3) {
                  const suggestions = await searchAddress(text);
                  setAddressSuggestions(suggestions);
                  setShowAddressSuggestions(suggestions.length > 0);
                } else {
                  setAddressSuggestions([]);
                  setShowAddressSuggestions(false);
                }
              }}
            />
            {showAddressSuggestions && addressSuggestions.length > 0 && (
              <View style={styles.suggestionsContainer}>
                <ScrollView style={styles.suggestionsList} nestedScrollEnabled>
                  {addressSuggestions.map((suggestion, index) => (
                    <TouchableOpacity
                      key={index}
                      style={styles.suggestionItem}
                      onPress={async () => {
                        setAddressSearchQuery(suggestion.address || suggestion.full_address || '');
                        setShowAddressSuggestions(false);
                        
                        if (suggestion.id) {
                          const details = await getAddressDetails(suggestion.id);
                          if (details) {
                            updateField('street', details.street || '');
                            updateField('suburb', details.suburb || '');
                            updateField('city', details.city || '');
                            updateField('postcode', details.postcode || '');
                          }
                        }
                      }}
                    >
                      <Text style={styles.suggestionText}>
                        {suggestion.address || suggestion.full_address || ''}
                      </Text>
                    </TouchableOpacity>
                  ))}
                </ScrollView>
              </View>
            )}
          </View>
          
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
  addressSearchContainer: {
    position: 'relative',
    marginBottom: 12,
  },
  suggestionsContainer: {
    position: 'absolute',
    top: '100%',
    left: 0,
    right: 0,
    zIndex: 1000,
    marginTop: 4,
    maxHeight: 200,
    backgroundColor: '#fff',
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#d1d5db',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 5,
  },
  suggestionsList: {
    maxHeight: 200,
  },
  suggestionItem: {
    padding: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e5e7eb',
  },
  suggestionText: {
    fontSize: 14,
    color: '#374151',
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
