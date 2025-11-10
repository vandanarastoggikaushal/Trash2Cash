import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TextInput,
  TouchableOpacity,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { APP_CONFIG } from '../config/api';
import { colors } from '../theme';

export default function RewardsScreen({ navigation }) {
  const [cansPerWeek, setCansPerWeek] = useState(10);
  const [appliances, setAppliances] = useState({
    washing_machine: 0,
    dishwasher: 0,
    microwave: 0,
    pc_case: 0,
    laptop: 0,
  });

  const cansReward = Math.floor((cansPerWeek * 52) / 100);
  const applianceReward = Object.entries(appliances).reduce((sum, [slug, qty]) => {
    return sum + (APP_CONFIG.APPLIANCE_CREDITS[slug]?.credit || 0) * qty;
  }, 0);
  const totalReward = cansReward + applianceReward;
  const monthlyReward = Math.round(totalReward / 12);
  const pickupReward = Math.round((cansPerWeek * 4) / 100);

  const updateAppliance = (slug, value) => {
    setAppliances({ ...appliances, [slug]: parseInt(value) || 0 });
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.section}>
        <Text style={styles.title}>💰 Rewards Calculator</Text>
        <Text style={styles.subtitle}>Calculate your yearly earnings</Text>

        {/* Cans Input */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>🥤 Cans per Week</Text>
          <TextInput
            style={styles.input}
            value={cansPerWeek.toString()}
            onChangeText={(text) => setCansPerWeek(parseInt(text) || 0)}
            keyboardType="numeric"
            placeholder="Enter cans per week"
          />
          <View style={styles.rewardRow}>
            <Text style={styles.rewardLabel}>Cans reward:</Text>
            <Text style={styles.rewardValue}>${cansReward}</Text>
          </View>
        </View>

        {/* Appliances Input */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>🔧 Appliances per Year</Text>
          {Object.entries(APP_CONFIG.APPLIANCE_CREDITS).map(([slug, data]) => (
            <View key={slug} style={styles.applianceRow}>
              <View style={styles.applianceInfo}>
                <Text style={styles.applianceLabel}>{data.label}</Text>
                <Text style={styles.applianceCredit}>${data.credit} each</Text>
              </View>
              <TextInput
                style={styles.applianceInput}
                value={appliances[slug].toString()}
                onChangeText={(value) => updateAppliance(slug, value)}
                keyboardType="numeric"
                placeholder="0"
              />
            </View>
          ))}
          <View style={styles.rewardRow}>
            <Text style={styles.rewardLabel}>Appliance credits:</Text>
            <Text style={styles.rewardValue}>${applianceReward}</Text>
          </View>
        </View>

        {/* Total */}
        <LinearGradient
          colors={['#15803d', '#22c55e']}
          style={styles.totalCard}
        >
          <Text style={styles.totalLabel}>Estimated Yearly Earnings</Text>
          <Text style={styles.totalValue}>${totalReward}</Text>
          <Text style={styles.totalSubtext}>
            Around ${monthlyReward} per month • ${pickupReward} per pickup
          </Text>
        </LinearGradient>

        {/* Info */}
        <View style={styles.infoCard}>
          <Text style={styles.infoText}>
            💡 Average NZ household ≈ $500/year in recyclable value!
          </Text>
        </View>

        <View style={styles.ctaCard}>
          <Text style={styles.ctaTitle}>Ready to turn cans into KiwiSaver or cash?</Text>
          <Text style={styles.ctaText}>
            Create your Trash2Cash account to lock in payouts and track balances. Already a member? Schedule your next recycling pickup in seconds.
          </Text>
          <View style={styles.ctaActions}>
            <TouchableOpacity
              style={styles.ctaPrimary}
              onPress={() => navigation.navigate('Register')}
            >
              <Text style={styles.ctaPrimaryText}>Create Account</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={styles.ctaSecondary}
              onPress={() => navigation.navigate('Schedule')}
            >
              <Text style={styles.ctaSecondaryText}>Schedule Pickup</Text>
            </TouchableOpacity>
          </View>
        </View>
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
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
    color: '#6b7280',
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
    marginBottom: 16,
  },
  applianceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#e5e7eb',
  },
  applianceInfo: {
    flex: 1,
  },
  applianceLabel: {
    fontSize: 16,
    color: '#374151',
    fontWeight: '500',
  },
  applianceCredit: {
    fontSize: 12,
    color: '#6b7280',
    marginTop: 4,
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
  rewardRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingTop: 16,
    marginTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#e5e7eb',
  },
  rewardLabel: {
    fontSize: 16,
    color: '#374151',
  },
  rewardValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: colors.brand,
  },
  totalCard: {
    padding: 24,
    borderRadius: 12,
    alignItems: 'center',
    marginBottom: 16,
  },
  totalLabel: {
    fontSize: 16,
    color: '#fff',
    marginBottom: 8,
  },
  totalValue: {
    fontSize: 36,
    fontWeight: 'bold',
    color: '#fff',
  },
  totalSubtext: {
    marginTop: 8,
    fontSize: 14,
    color: '#dcfce7',
  },
  infoCard: {
    backgroundColor: '#ecfdf5',
    padding: 16,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: colors.brand,
  },
  infoText: {
    fontSize: 14,
    color: '#065f46',
  },
  ctaCard: {
    marginTop: 24,
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 24,
    borderWidth: 1,
    borderColor: '#bbf7d0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
    elevation: 3,
    gap: 12,
  },
  ctaTitle: {
    fontSize: 22,
    fontWeight: '700',
    color: '#1f2937',
  },
  ctaText: {
    fontSize: 14,
    color: '#374151',
    lineHeight: 20,
  },
  ctaActions: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  ctaPrimary: {
    backgroundColor: colors.brand,
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 12,
  },
  ctaPrimaryText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '700',
  },
  ctaSecondary: {
    backgroundColor: '#ecfdf5',
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 12,
  },
  ctaSecondaryText: {
    color: colors.brand,
    fontSize: 14,
    fontWeight: '700',
  },
});

