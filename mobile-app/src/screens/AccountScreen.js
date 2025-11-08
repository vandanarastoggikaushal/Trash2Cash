import React, { useContext, useEffect, useState } from 'react';
import { ScrollView, View, Text, StyleSheet, TouchableOpacity, TextInput, Alert, ActivityIndicator } from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { colors } from '../theme';

function formatAddress(address) {
  if (!address) return '';
  const { street, suburb, city, postcode } = address;
  return [street, suburb, `${city} ${postcode}`.trim()].filter(Boolean).join('\n');
}

export default function AccountScreen({ navigation }) {
  const {
    user,
    isAuthenticated,
    signOut,
    updateProfile,
    refreshProfile,
    loading,
    authError,
    clearError,
  } = useContext(AuthContext);

  const [savingAddress, setSavingAddress] = useState(false);
  const [savingPayout, setSavingPayout] = useState(false);
  const [addressForm, setAddressForm] = useState({
    phone: '',
    street: '',
    suburb: '',
    city: '',
    postcode: '',
    marketingOptIn: true,
  });
  const [payoutForm, setPayoutForm] = useState({
    payoutMethod: 'bank',
    bankName: '',
    bankAccount: '',
    childName: '',
    childBankAccount: '',
    kiwisaverProvider: '',
    kiwisaverMemberId: '',
  });

  useEffect(() => {
    clearError?.();
  }, []);

  useEffect(() => {
    if (user) {
      setAddressForm({
        phone: user.phone || '',
        street: user.address?.street || '',
        suburb: user.address?.suburb || '',
        city: user.address?.city || '',
        postcode: user.address?.postcode || '',
        marketingOptIn: !!user.marketingOptIn,
      });
      setPayoutForm({
        payoutMethod: user.payout?.method || 'bank',
        bankName: user.payout?.bankName || '',
        bankAccount: user.payout?.bankAccount || '',
        childName: user.payout?.childName || '',
        childBankAccount: user.payout?.childBankAccount || '',
        kiwisaverProvider: user.payout?.kiwisaverProvider || '',
        kiwisaverMemberId: user.payout?.kiwisaverMemberId || '',
      });
    }
  }, [user]);

  const handleAddressSave = async () => {
    if (!isAuthenticated) return;
    if (!addressForm.street || !addressForm.suburb || !addressForm.city || !addressForm.postcode) {
      Alert.alert('Address required', 'Please provide your street, suburb, city and postcode.');
      return;
    }
    setSavingAddress(true);
    const result = await updateProfile({
      street: addressForm.street,
      suburb: addressForm.suburb,
      city: addressForm.city,
      postcode: addressForm.postcode,
      phone: addressForm.phone,
      marketingOptIn: addressForm.marketingOptIn,
    });
    setSavingAddress(false);
    if (result?.success) {
      Alert.alert('Saved', 'Your address and contact details have been updated.');
      refreshProfile();
    } else if (result?.error) {
      Alert.alert('Update failed', result.error);
    }
  };

  const handlePayoutSave = async () => {
    if (!isAuthenticated) return;
    if (payoutForm.payoutMethod === 'bank' && (!payoutForm.bankName || !payoutForm.bankAccount)) {
      Alert.alert('Bank details required', 'Please provide your bank name and account number.');
      return;
    }
    if (payoutForm.payoutMethod === 'child_account' && !payoutForm.childName) {
      Alert.alert('Child name required', 'Please provide the child name for payouts.');
      return;
    }
    if (payoutForm.payoutMethod === 'kiwisaver' && (!payoutForm.kiwisaverProvider || !payoutForm.kiwisaverMemberId)) {
      Alert.alert('KiwiSaver details required', 'Please provide your KiwiSaver provider and member ID.');
      return;
    }

    setSavingPayout(true);
    const result = await updateProfile({
      payoutMethod: payoutForm.payoutMethod,
      bankName: payoutForm.bankName,
      bankAccount: payoutForm.bankAccount,
      childName: payoutForm.childName,
      childBankAccount: payoutForm.childBankAccount,
      kiwisaverProvider: payoutForm.kiwisaverProvider,
      kiwisaverMemberId: payoutForm.kiwisaverMemberId,
    });
    setSavingPayout(false);
    if (result?.success) {
      Alert.alert('Saved', 'Your payout preferences have been updated.');
      refreshProfile();
    } else if (result?.error) {
      Alert.alert('Update failed', result.error);
    }
  };

  if (!isAuthenticated) {
    return (
      <View style={styles.centered}>
        <Text style={styles.title}>Account</Text>
        <Text style={styles.subtitle}>
          Log in to manage your contact details, payout preferences and schedule pickups.
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

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>Hello, {user?.firstName || user?.username}</Text>
      <Text style={styles.subtitle}>
        Keep your address and payout information up to date to make pickups quick and easy.
      </Text>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Account Summary</Text>
        <View style={styles.summaryRow}>
          <Text style={styles.summaryLabel}>Email</Text>
          <Text style={styles.summaryValue}>{user?.email || '—'}</Text>
        </View>
        <View style={styles.summaryRow}>
          <Text style={styles.summaryLabel}>Phone</Text>
          <Text style={styles.summaryValue}>{user?.phone || '—'}</Text>
        </View>
        <View style={styles.summaryRow}>
          <Text style={styles.summaryLabel}>Address</Text>
          <Text style={styles.summaryValue}>{formatAddress(user?.address) || '—'}</Text>
        </View>
        <View style={styles.summaryRow}>
          <Text style={styles.summaryLabel}>Payout method</Text>
          <Text style={styles.summaryValue}>{user?.payout?.method || 'Bank account'}</Text>
        </View>
      </View>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Update Address & Contact</Text>
        <TextInput
          style={styles.input}
          placeholder="Phone"
          value={addressForm.phone}
          onChangeText={(text) => setAddressForm((prev) => ({ ...prev, phone: text }))}
        />
        <TextInput
          style={styles.input}
          placeholder="Street"
          value={addressForm.street}
          onChangeText={(text) => setAddressForm((prev) => ({ ...prev, street: text }))}
        />
        <TextInput
          style={styles.input}
          placeholder="Suburb"
          value={addressForm.suburb}
          onChangeText={(text) => setAddressForm((prev) => ({ ...prev, suburb: text }))}
        />
        <View style={styles.row}>
          <TextInput
            style={[styles.input, styles.half]}
            placeholder="City"
            value={addressForm.city}
            onChangeText={(text) => setAddressForm((prev) => ({ ...prev, city: text }))}
          />
          <TextInput
            style={[styles.input, styles.half]}
            placeholder="Postcode"
            keyboardType="number-pad"
            maxLength={4}
            value={addressForm.postcode}
            onChangeText={(text) => setAddressForm((prev) => ({ ...prev, postcode: text }))}
          />
        </View>
        <TouchableOpacity
          style={styles.toggle}
          onPress={() => setAddressForm((prev) => ({ ...prev, marketingOptIn: !prev.marketingOptIn }))}
        >
          <Text style={styles.toggleText}>
            {addressForm.marketingOptIn ? '✅ Subscribed to updates' : '☐ Subscribe to updates'}
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.primaryButton, savingAddress && { opacity: 0.7 }]}
          onPress={handleAddressSave}
          disabled={savingAddress}
        >
          {savingAddress ? <ActivityIndicator color="#fff" /> : <Text style={styles.primaryButtonText}>Save address</Text>}
        </TouchableOpacity>
      </View>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Payout Preferences</Text>
        <View style={styles.payoutRow}>
          {[
            { value: 'bank', label: 'Bank' },
            { value: 'child_account', label: 'Child' },
            { value: 'kiwisaver', label: 'KiwiSaver' },
          ].map((option) => (
            <TouchableOpacity
              key={option.value}
              style={[
                styles.payoutOption,
                payoutForm.payoutMethod === option.value && styles.payoutOptionActive,
              ]}
              onPress={() => setPayoutForm((prev) => ({ ...prev, payoutMethod: option.value }))}
            >
              <Text
                style={[
                  styles.payoutOptionText,
                  payoutForm.payoutMethod === option.value && styles.payoutOptionTextActive,
                ]}
              >
                {option.label}
              </Text>
            </TouchableOpacity>
          ))}
        </View>

        {payoutForm.payoutMethod === 'bank' && (
          <>
            <TextInput
              style={styles.input}
              placeholder="Bank name"
              value={payoutForm.bankName}
              onChangeText={(text) => setPayoutForm((prev) => ({ ...prev, bankName: text }))}
            />
            <TextInput
              style={styles.input}
              placeholder="Account number (e.g. 12-1234-1234567-00)"
              value={payoutForm.bankAccount}
              onChangeText={(text) => setPayoutForm((prev) => ({ ...prev, bankAccount: text }))}
            />
          </>
        )}

        {payoutForm.payoutMethod === 'child_account' && (
          <>
            <TextInput
              style={styles.input}
              placeholder="Child name"
              value={payoutForm.childName}
              onChangeText={(text) => setPayoutForm((prev) => ({ ...prev, childName: text }))}
            />
            <TextInput
              style={styles.input}
              placeholder="Child bank account (optional)"
              value={payoutForm.childBankAccount}
              onChangeText={(text) => setPayoutForm((prev) => ({ ...prev, childBankAccount: text }))}
            />
          </>
        )}

        {payoutForm.payoutMethod === 'kiwisaver' && (
          <>
            <TextInput
              style={styles.input}
              placeholder="KiwiSaver provider"
              value={payoutForm.kiwisaverProvider}
              onChangeText={(text) => setPayoutForm((prev) => ({ ...prev, kiwisaverProvider: text }))}
            />
            <TextInput
              style={styles.input}
              placeholder="KiwiSaver member ID"
              value={payoutForm.kiwisaverMemberId}
              onChangeText={(text) => setPayoutForm((prev) => ({ ...prev, kiwisaverMemberId: text }))}
            />
          </>
        )}

        <TouchableOpacity
          style={[styles.primaryButton, savingPayout && { opacity: 0.7 }]}
          onPress={handlePayoutSave}
          disabled={savingPayout}
        >
          {savingPayout ? <ActivityIndicator color="#fff" /> : <Text style={styles.primaryButtonText}>Save payout settings</Text>}
        </TouchableOpacity>
      </View>

      {authError && <Text style={styles.errorText}>{authError}</Text>}

      <TouchableOpacity
        style={styles.secondaryButton}
        onPress={() => {
          Alert.alert('Logout', 'Are you sure you want to log out?', [
            { text: 'Cancel', style: 'cancel' },
            { text: 'Logout', style: 'destructive', onPress: signOut },
          ]);
        }}
      >
        <Text style={styles.secondaryButtonText}>Logout</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 24,
    backgroundColor: '#f3f4f6',
  },
  centered: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
    backgroundColor: '#f3f4f6',
  },
  title: {
    fontSize: 26,
    fontWeight: '700',
    color: colors.brand,
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 15,
    color: '#4b5563',
    marginBottom: 20,
    textAlign: 'center',
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 20,
    marginBottom: 18,
    shadowColor: '#000',
    shadowOpacity: 0.05,
    shadowRadius: 12,
    elevation: 3,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: '700',
    marginBottom: 12,
    color: '#111827',
  },
  summaryRow: {
    marginBottom: 10,
  },
  summaryLabel: {
    fontSize: 12,
    fontWeight: '600',
    color: '#6b7280',
    textTransform: 'uppercase',
  },
  summaryValue: {
    fontSize: 15,
    color: '#111827',
    marginTop: 2,
  },
  input: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 16,
    fontSize: 16,
    marginBottom: 12,
  },
  row: {
    flexDirection: 'row',
    gap: 12,
  },
  half: {
    flex: 1,
  },
  toggle: {
    paddingVertical: 10,
    alignItems: 'center',
    borderRadius: 999,
    backgroundColor: '#f9fafb',
    borderWidth: 1,
    borderColor: '#e5e7eb',
    marginBottom: 12,
  },
  toggleText: {
    fontWeight: '600',
    color: colors.brand,
  },
  payoutRow: {
    flexDirection: 'row',
    gap: 8,
    marginBottom: 12,
  },
  payoutOption: {
    flex: 1,
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 999,
    paddingVertical: 10,
    alignItems: 'center',
  },
  payoutOptionActive: {
    backgroundColor: '#dcfce7',
    borderColor: colors.brand,
  },
  payoutOptionText: {
    fontWeight: '600',
    color: '#4b5563',
  },
  payoutOptionTextActive: {
    color: colors.brand,
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
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 8,
    marginBottom: 24,
  },
  secondaryButtonText: {
    color: colors.brand,
    fontWeight: '700',
    fontSize: 16,
  },
  notice: {
    backgroundColor: '#dcfce7',
    borderColor: '#bbf7d0',
  },
  noticeText: {
    color: '#15803d',
    fontWeight: '600',
    fontSize: 14,
  },
  errorText: {
    color: '#dc2626',
    textAlign: 'center',
    marginTop: 12,
    marginBottom: 12,
    fontWeight: '600',
  },
});

