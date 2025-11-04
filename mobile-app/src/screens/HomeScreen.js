import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  TextInput,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { APP_CONFIG } from '../config/api';
import { colors } from '../theme';

export default function HomeScreen({ navigation }) {
  const [cansPerWeek, setCansPerWeek] = useState(10);
  const cansReward = Math.floor((cansPerWeek * 52) / 100);

  return (
    <ScrollView style={styles.container}>
      {/* Hero Section */}
      <LinearGradient
        colors={['#15803d', '#22c55e']}
        style={styles.hero}
      >
        <Text style={styles.heroTitle}>Turn Your Trash</Text>
        <Text style={styles.heroSubtitle}>Into Cash or KiwiSaver</Text>
        <Text style={styles.heroText}>
          Earn $1 for every 100 cans—deposit to kids' accounts or KiwiSaver
        </Text>
        <TouchableOpacity
          style={styles.ctaButton}
          onPress={() => navigation.navigate('Schedule')}
        >
          <Text style={styles.ctaButtonText}>Schedule a Pickup</Text>
        </TouchableOpacity>
      </LinearGradient>

      {/* Quick Calculator */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Quick Calculator</Text>
        <View style={styles.calculator}>
          <Text style={styles.label}>Cans per week:</Text>
          <TextInput
            style={styles.input}
            value={cansPerWeek.toString()}
            onChangeText={(text) => setCansPerWeek(parseInt(text) || 0)}
            keyboardType="numeric"
          />
          <View style={styles.result}>
            <Text style={styles.resultLabel}>Estimated yearly reward:</Text>
            <Text style={styles.resultValue}>${cansReward}</Text>
          </View>
        </View>
      </View>

      {/* Features */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Why Trash2Cash?</Text>
        <FeatureCard
          icon="truck-delivery"
          title="Door-to-door pickup"
          description="Across Wellington & suburbs"
        />
        <FeatureCard
          icon="cash"
          title="$1 per 100 cans"
          description="Simple and transparent"
        />
        <FeatureCard
          icon="heart"
          title="Kids & KiwiSaver"
          description="Grow value over time"
        />
      </View>

      {/* What We Collect */}
      <View style={styles.section}>
        <Text style={styles.sectionTitle}>What We Collect</Text>
        <View style={styles.itemsGrid}>
          {['Aluminium cans', 'Washing machines', 'Microwaves', 'PC cases', 'Laptops', 'Dishwashers'].map((item, index) => (
            <View key={index} style={styles.itemCard}>
              <Text style={styles.itemText}>{item}</Text>
            </View>
          ))}
        </View>
      </View>

      {/* CTA */}
      <View style={styles.section}>
        <TouchableOpacity
          style={styles.ctaButtonSecondary}
          onPress={() => navigation.navigate('HowItWorks')}
        >
          <Text style={styles.ctaButtonSecondaryText}>How It Works</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

function FeatureCard({ icon, title, description }) {
  return (
    <View style={styles.featureCard}>
      <MaterialCommunityIcons name={icon} size={32} color={colors.brand} />
      <Text style={styles.featureTitle}>{title}</Text>
      <Text style={styles.featureDescription}>{description}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f9fafb',
  },
  hero: {
    padding: 24,
    paddingTop: 40,
    alignItems: 'center',
  },
  heroTitle: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#fff',
    textAlign: 'center',
    marginBottom: 8,
  },
  heroSubtitle: {
    fontSize: 24,
    color: '#fff',
    textAlign: 'center',
    marginBottom: 16,
  },
  heroText: {
    fontSize: 16,
    color: '#fff',
    textAlign: 'center',
    marginBottom: 24,
    opacity: 0.95,
  },
  ctaButton: {
    backgroundColor: '#fff',
    paddingHorizontal: 32,
    paddingVertical: 16,
    borderRadius: 12,
    marginTop: 8,
  },
  ctaButtonText: {
    color: colors.brand,
    fontSize: 18,
    fontWeight: 'bold',
  },
  section: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 16,
  },
  calculator: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  label: {
    fontSize: 16,
    color: '#374151',
    marginBottom: 8,
  },
  input: {
    borderWidth: 1,
    borderColor: '#d1d5db',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    marginBottom: 16,
  },
  result: {
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#e5e7eb',
  },
  resultLabel: {
    fontSize: 14,
    color: '#6b7280',
    marginBottom: 4,
  },
  resultValue: {
    fontSize: 28,
    fontWeight: 'bold',
    color: colors.brand,
  },
  featureCard: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    marginBottom: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  featureTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1f2937',
    marginTop: 12,
    marginBottom: 4,
  },
  featureDescription: {
    fontSize: 14,
    color: '#6b7280',
    textAlign: 'center',
  },
  itemsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  itemCard: {
    backgroundColor: '#fff',
    padding: 16,
    borderRadius: 8,
    marginBottom: 12,
    width: '48%',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  itemText: {
    fontSize: 14,
    color: '#374151',
    fontWeight: '500',
  },
  ctaButtonSecondary: {
    backgroundColor: colors.brand,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  ctaButtonSecondaryText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
