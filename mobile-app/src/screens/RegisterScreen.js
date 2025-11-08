import React, { useContext, useState, useEffect } from 'react';
import { ScrollView, View, Text, TextInput, StyleSheet, TouchableOpacity, ActivityIndicator, KeyboardAvoidingView, Platform, Alert } from 'react-native';
import { AuthContext } from '../context/AuthContext';
import { APP_CONFIG } from '../config/api';
import { colors } from '../theme';

const initialForm = {
  username: '',
  email: '',
  password: '',
  passwordConfirm: '',
  firstName: '',
  lastName: '',
  phone: '',
  street: '',
  suburb: '',
  city: APP_CONFIG.CITY,
  postcode: '',
  marketingOptIn: true,
  payoutMethod: 'bank',
  bankName: '',
  bankAccount: '',
  childName: '',
  childBankAccount: '',
  kiwisaverProvider: '',
  kiwisaverMemberId: '',
};

export default function RegisterScreen({ navigation }) {
  const { signUp, loading, authError, clearError, isAuthenticated } = useContext(AuthContext);
  const [form, setForm] = useState(initialForm);
  const [localError, setLocalError] = useState('');

  useEffect(() => {
    clearError?.();
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      navigation.reset({
        index: 0,
        routes: [{ name: 'Main', params: { screen: 'Account' } }],
      });
    }
  }, [isAuthenticated, navigation]);

  const updateField = (field, value) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const validate = () => {
    if (!form.username || form.username.length < 3) {
      return 'Username must be at least 3 characters.';
    }
    if (!form.firstName || !form.lastName) {
      return 'First and last name are required.';
    }
    if (!form.phone) {
      return 'Phone number is required.';
    }
    if (!form.street || !form.suburb || !form.city || !form.postcode) {
      return 'Street, suburb, city and postcode are required.';
    }
    if (!/^\d{4}$/.test(form.postcode)) {
      return 'Postcode must be a 4-digit NZ postcode.';
    }
    if (!form.email || !form.email.includes('@')) {
      return 'Please enter a valid email address.';
    }
    if (!form.password || form.password.length < 6) {
      return 'Password must be at least 6 characters.';
    }
    if (form.password !== form.passwordConfirm) {
      return 'Passwords do not match.';
    }
    if (form.payoutMethod === 'bank') {
      if (!form.bankName || !form.bankAccount) {
        return 'Bank name and account are required.';
      }
    } else if (form.payoutMethod === 'child_account' && !form.childName) {
      return 'Child name is required for child account payouts.';
    } else if (form.payoutMethod === 'kiwisaver' && (!form.kiwisaverProvider || !form.kiwisaverMemberId)) {
      return 'KiwiSaver provider and member ID are required.';
    }
    return '';
  };

  const handleSubmit = async () => {
    const error = validate();
    if (error) {
      setLocalError(error);
      return;
    }
    setLocalError('');
    const result = await signUp(form);
    if (result?.success) {
      Alert.alert('Welcome!', 'Your Trash2Cash account has been created.');
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.flex}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <ScrollView contentContainerStyle={styles.container}>
        <Text style={styles.title}>Register for Trash2Cash</Text>
        <Text style={styles.subtitle}>Create an account to manage pickups and track payouts.</Text>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>Account Details</Text>
          <TextInput
            style={styles.input}
            placeholder="Username *"
            autoCapitalize="none"
            value={form.username}
            onChangeText={(text) => updateField('username', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Email *"
            keyboardType="email-address"
            value={form.email}
            onChangeText={(text) => updateField('email', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Password *"
            secureTextEntry
            value={form.password}
            onChangeText={(text) => updateField('password', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Confirm password *"
            secureTextEntry
            value={form.passwordConfirm}
            onChangeText={(text) => updateField('passwordConfirm', text)}
          />
        </View>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>Contact & Address</Text>
          <View style={styles.row}>
            <TextInput
              style={[styles.input, styles.half]}
              placeholder="First name *"
              value={form.firstName}
              onChangeText={(text) => updateField('firstName', text)}
            />
            <TextInput
              style={[styles.input, styles.half]}
              placeholder="Last name *"
              value={form.lastName}
              onChangeText={(text) => updateField('lastName', text)}
            />
          </View>
          <TextInput
            style={styles.input}
            placeholder="Phone *"
            keyboardType="phone-pad"
            value={form.phone}
            onChangeText={(text) => updateField('phone', text)}
          />
          <TextInput
            style={styles.input}
            placeholder="Street *"
            value={form.street}
            onChangeText={(text) => updateField('street', text)}
          />
          <View style={styles.row}>
            <TextInput
              style={[styles.input, styles.half]}
              placeholder="Suburb *"
              value={form.suburb}
              onChangeText={(text) => updateField('suburb', text)}
            />
            <TextInput
              style={[styles.input, styles.half]}
              placeholder="City *"
              value={form.city}
              onChangeText={(text) => updateField('city', text)}
            />
          </View>
          <TextInput
            style={styles.input}
            placeholder="Postcode *"
            keyboardType="number-pad"
            maxLength={4}
            value={form.postcode}
            onChangeText={(text) => updateField('postcode', text)}
          />
          <View style={styles.switchRow}>
            <Text>Receive Trash2Cash updates</Text>
            <TouchableOpacity
              onPress={() => updateField('marketingOptIn', !form.marketingOptIn)}
              style={[styles.toggle, form.marketingOptIn && styles.toggleActive]}
            >
              <Text style={styles.toggleText}>{form.marketingOptIn ? 'Yes' : 'No'}</Text>
            </TouchableOpacity>
          </View>
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
                  form.payoutMethod === option.value && styles.payoutOptionActive,
                ]}
                onPress={() => updateField('payoutMethod', option.value)}
              >
                <Text
                  style={[
                    styles.payoutOptionText,
                    form.payoutMethod === option.value && styles.payoutOptionTextActive,
                  ]}
                >
                  {option.label}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          {form.payoutMethod === 'bank' && (
            <>
              <TextInput
                style={styles.input}
                placeholder="Bank name *"
                value={form.bankName}
                onChangeText={(text) => updateField('bankName', text)}
              />
              <TextInput
                style={styles.input}
                placeholder="Account number * (e.g. 12-1234-1234567-00)"
                value={form.bankAccount}
                onChangeText={(text) => updateField('bankAccount', text)}
              />
            </>
          )}

          {form.payoutMethod === 'child_account' && (
            <>
              <TextInput
                style={styles.input}
                placeholder="Child name *"
                value={form.childName}
                onChangeText={(text) => updateField('childName', text)}
              />
              <TextInput
                style={styles.input}
                placeholder="Child bank account (optional)"
                value={form.childBankAccount}
                onChangeText={(text) => updateField('childBankAccount', text)}
              />
            </>
          )}

          {form.payoutMethod === 'kiwisaver' && (
            <>
              <TextInput
                style={styles.input}
                placeholder="KiwiSaver provider *"
                value={form.kiwisaverProvider}
                onChangeText={(text) => updateField('kiwisaverProvider', text)}
              />
              <TextInput
                style={styles.input}
                placeholder="KiwiSaver member ID *"
                value={form.kiwisaverMemberId}
                onChangeText={(text) => updateField('kiwisaverMemberId', text)}
              />
            </>
          )}
        </View>

        {(localError || authError) && (
          <Text style={styles.errorText}>{localError || authError}</Text>
        )}

        <TouchableOpacity
          style={[styles.submitButton, loading && { opacity: 0.7 }]}
          onPress={handleSubmit}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.submitText}>Create Account</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity onPress={() => navigation.navigate('Login')}>
          <Text style={styles.linkText}>Already have an account? Login</Text>
        </TouchableOpacity>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  flex: { flex: 1 },
  container: {
    padding: 24,
    backgroundColor: '#f3f4f6',
  },
  title: {
    fontSize: 26,
    fontWeight: '700',
    color: colors.brand,
    marginBottom: 6,
  },
  subtitle: {
    fontSize: 15,
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
    shadowRadius: 10,
    elevation: 3,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: '#111827',
    marginBottom: 12,
  },
  input: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 16,
    fontSize: 16,
    marginBottom: 12,
    backgroundColor: '#fff',
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 12,
  },
  half: {
    flex: 1,
  },
  switchRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 8,
  },
  toggle: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 999,
    borderWidth: 1,
    borderColor: '#d1d5db',
  },
  toggleActive: {
    backgroundColor: '#dcfce7',
    borderColor: colors.brand,
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
  errorText: {
    color: '#dc2626',
    marginBottom: 12,
    fontWeight: '600',
  },
  submitButton: {
    backgroundColor: colors.brand,
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 8,
  },
  submitText: {
    color: '#fff',
    fontWeight: '700',
    fontSize: 16,
  },
  linkText: {
    marginTop: 16,
    color: colors.brand,
    textAlign: 'center',
    fontWeight: '600',
  },
});

