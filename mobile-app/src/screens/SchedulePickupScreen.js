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

const SCHEDULE_WINDOWS = ['Morning', 'Afternoon', 'Evening'];
const PICKUP_TYPES = [
  { value: 'cans', label: '🥤 Cans' },
  { value: 'appliances', label: '🔧 Appliances' },
  { value: 'both', label: '♻️ Both' },
];

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
    const quantity = Number.parseInt(value, 10) || 0;
    setFormData((prev) => ({
      ...prev,
      appliances: { ...prev.appliances, [slug]: quantity },
    }));
  };

  const calculateCansReward = () => {
    const cans = Number.parseInt(formData.cansEstimate, 10) || 0;
    return Math.floor(cans / 100);
  };

  const calculateApplianceReward = () =>
    Object.entries(formData.appliances).reduce((sum, [slug, qty]) => {
      const credit = APP_CONFIG.APPLIANCE_CREDITS[slug]?.credit || 0;
      return sum + credit * (qty || 0);
    }, 0);

  const accountAddress = user?.address || {};
  const payout = user?.payout || {};

  const hasAddress =
    !!accountAddress.street &&
    !!accountAddress.suburb &&
    !!accountAddress.city &&
    !!accountAddress.postcode;

  const payoutValid = (() => {
    const method = payout.method || 'bank';
    switch (method) {
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
        fullName: `${user?.firstName || ''} ${user?.lastName || ''}`.trim() || user?.username || '',
        email: user?.email || '',
        phone: user?.phone || '',
        marketingOptIn: !!user?.marketingOptIn,
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
          [{ text: 'OK', onPress: resetPickupForm }],
        );
      } else {
        Alert.alert('Error', result.error || 'Failed to submit request. Please try again.');
      }
    } catch (error) {
      console.error('[SchedulePickup] submission error', error);
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
          Please log in so we can confirm your address and payout details before you schedule a pickup.
        </Text>
        <TouchableOpacity style={styles.primaryButton} onPress={() => navigation.navigate('Login')}>
          <Text style={styles.primaryButtonText}>Login</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.secondaryButton} onPress={() => navigation.navigate('Register')}>
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
          We’ll use the address and payout details saved in your account for this pickup request.
        </Text>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>Account summary</Text>
          <Text style={styles.summaryLabel}>Name</Text>
          <Text style={styles.summaryValue}>
            {`${user?.firstName || ''} ${user?.lastName || ''}`.trim() || user?.username || '—'}
          </Text>
          <Text style={styles.summaryLabel}>Email</Text>
          <Text style={styles.summaryValue}>{user?.email || '—'}</Text>
          <Text style={styles.summaryLabel}>Phone</Text>
          <Text style={styles.summaryValue}>{user?.phone || '—'}</Text>
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
            {PICKUP_TYPES.map((type) => (
              <TouchableOpacity
                key={type.value}
                style={[styles.pillButton, formData.pickupType === type.value && styles.pillButtonActive]}
                onPress={() => updateField('pickupType', type.value)}
              >
                <Text
                  style={[styles.pillText, formData.pickupType === type.value && styles.pillTextActive]}
                >
                  {type.label}
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
              <Text style={styles.helperText}>💡 $1 credit per 100 cans</Text>
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
                    placeholder="0"
                  />
                </View>
              ))}
              <Text style={styles.helperText}>
                Total appliance credit: ${calculateApplianceReward()}
              </Text>
            </View>
          )}

          <View style={styles.dateRow}>
            <TouchableOpacity style={styles.dateButton} onPress={() => setShowDatePicker(true)}>
              <MaterialCommunityIcons name="calendar" size={20} color={colors.brand} />
              <Text style={styles.dateButtonText}>
                {formData.preferredDate
                  ? `Preferred date: ${new Date(formData.preferredDate).toLocaleDateString()}`
                  : 'Select preferred date'}
              </Text>
            </TouchableOpacity>
            <View style={styles.pillRow}>
              {SCHEDULE_WINDOWS.map((window) => (
                <TouchableOpacity
                  key={window}
                  style={[styles.pillButtonSmall, formData.preferredWindow === window && styles.pillButtonActive]}
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
          minimumDate={new Date()}
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
    flexWrap: 'wrap',
    columnGap: 8,
    rowGap: 8,
    marginBottom: 12,
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
  input: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 16,
    marginBottom: 12,
    backgroundColor: '#fff',
  },
  applianceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#e5e7eb',
  },
  applianceLabel: {
    fontSize: 15,
    color: '#374151',
  },
  applianceInput: {
    width: 70,
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 12,
    padding: 10,
    textAlign: 'center',
    backgroundColor: '#fff',
  },
  rewardBox: {
    minWidth: 100,
    backgroundColor: '#ecfdf5',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 16,
    alignItems: 'center',
    marginTop: 8,
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
    columnGap: 8,
  },
  dateButtonText: {
    fontWeight: '600',
    color: '#1f2937',
  },
  checkboxRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
    columnGap: 12,
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
    paddingHorizontal: 24,
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
    paddingHorizontal: 24,
    alignItems: 'center',
    marginTop: 12,
  },
  secondaryButtonText: {
    color: colors.brand,
    fontWeight: '700',
    fontSize: 16,
  },
  textArea: {
    height: 110,
    textAlignVertical: 'top',
  },
});

