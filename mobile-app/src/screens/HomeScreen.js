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
  const keywordChips = [
    'Recycling Wellington pickup',
    'Trash collection for cans',
    'Recycle collection near me',
    'Wellington metal recycling',
    'Appliance recycling NZ',
    'Doorstep recycling service',
  ];

  return (
    <ScrollView style={styles.container}>
      {/* Hero Section */}
      <LinearGradient
        colors={['#15803d', '#22c55e']}
        style={styles.hero}
      >
        <Text style={styles.heroTitle}>Wellington Recycling</Text>
        <Text style={styles.heroSubtitle}>Pickup &amp; Trash Collection</Text>
        <Text style={styles.heroText}>
          Trash2Cash NZ collects aluminium cans, appliances and scrap metal across Wellington. Earn $1 for every 100 cans and send it to kids' savings or KiwiSaver.
        </Text>
        <TouchableOpacity
          style={styles.ctaButton}
          onPress={() => navigation.navigate('Schedule')}
        >
          <Text style={styles.ctaButtonText}>Schedule a Pickup</Text>
        </TouchableOpacity>
      </LinearGradient>

      <View style={styles.section}>
        <View style={styles.infoCard}>
          <Text style={styles.infoTitle}>Keep Wellington cans &amp; appliances out of landfill</Text>
          <Text style={styles.infoText}>
            Searching for recycling or trash collection in Wellington? Trash2Cash NZ provides a friendly, local pickup service.
            Book a time, leave rinsed cans or disconnected appliances at your doorway, and we will take care of the recycling and the rewards.
          </Text>
          <View style={styles.bulletList}>
            <Bullet text="Certified recycling partners for aluminium, appliances and scrap metals." />
            <Bullet text="Fast turnaround across Wellington City, Hutt Valley, Porirua and Kapiti suburbs." />
            <Bullet text="Transparent pricing with instant digital receipts." />
          </View>
          <View style={styles.linkRow}>
            <TouchableOpacity
              style={styles.linkButton}
              onPress={() => navigation.navigate('WellingtonRecycling')}
            >
              <Text style={styles.linkButtonText}>Learn about our Wellington service</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={styles.linkButtonSecondary}
              onPress={() => navigation.navigate('FAQ')}
            >
              <Text style={styles.linkButtonSecondaryText}>Read recycling FAQs</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Popular recycling searches we answer</Text>
        <View style={styles.chipsWrap}>
          {keywordChips.map((chip) => (
            <View key={chip} style={styles.chip}>
              <Text style={styles.chipText}>{chip}</Text>
            </View>
          ))}
        </View>
      </View>

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

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Wellington suburbs we visit</Text>
        <View style={styles.serviceGrid}>
          {APP_CONFIG.SERVICE_AREAS.map((area) => (
            <View key={area} style={styles.serviceCard}>
              <Text style={styles.serviceCardLabel}>Service area</Text>
              <Text style={styles.serviceCardTitle}>{area}</Text>
            </View>
          ))}
        </View>
      </View>

      <View style={styles.section}>
        <View style={styles.resourcesCard}>
          <Text style={styles.resourcesTitle}>Recycling guides &amp; resources</Text>
          <Text style={styles.resourcesText}>
            Get expert tips on preparing cans, booking pickups, and running community recycling drives. New guides are added regularly.
          </Text>
          <TouchableOpacity
            style={styles.linkButton}
            onPress={() => navigation.navigate('Resources')}
          >
            <Text style={styles.linkButtonText}>Browse recycling resources</Text>
          </TouchableOpacity>
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

function Bullet({ text }) {
  return (
    <View style={styles.bulletItem}>
      <View style={styles.bulletDot} />
      <Text style={styles.bulletText}>{text}</Text>
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
  infoCard: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 24,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 6,
    elevation: 3,
    borderWidth: 1,
    borderColor: '#bbf7d0',
  },
  infoTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 12,
  },
  infoText: {
    fontSize: 15,
    lineHeight: 22,
    color: '#374151',
    marginBottom: 16,
  },
  bulletList: {
    marginBottom: 16,
    gap: 10,
  },
  bulletItem: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 10,
  },
  bulletDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: colors.brand,
    marginTop: 6,
  },
  bulletText: {
    flex: 1,
    fontSize: 14,
    color: '#374151',
    lineHeight: 20,
  },
  linkRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  linkButton: {
    backgroundColor: colors.brand,
    paddingVertical: 12,
    paddingHorizontal: 18,
    borderRadius: 12,
  },
  linkButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '700',
  },
  linkButtonSecondary: {
    backgroundColor: '#ecfdf5',
    paddingVertical: 12,
    paddingHorizontal: 18,
    borderRadius: 12,
  },
  linkButtonSecondaryText: {
    color: colors.brand,
    fontSize: 14,
    fontWeight: '700',
  },
  chipsWrap: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
  },
  chip: {
    paddingHorizontal: 14,
    paddingVertical: 8,
    borderRadius: 999,
    backgroundColor: '#dcfce7',
    borderWidth: 1,
    borderColor: '#bbf7d0',
  },
  chipText: {
    fontSize: 13,
    fontWeight: '600',
    color: '#15803d',
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
  serviceGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  serviceCard: {
    width: '48%',
    backgroundColor: '#ffffff',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#bbf7d0',
    padding: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 3,
    elevation: 2,
  },
  serviceCardLabel: {
    fontSize: 12,
    textTransform: 'uppercase',
    color: '#16a34a',
    fontWeight: '600',
  },
  serviceCardTitle: {
    marginTop: 6,
    fontSize: 16,
    fontWeight: '700',
    color: '#1f2937',
  },
  resourcesCard: {
    backgroundColor: '#f0fdf4',
    borderRadius: 16,
    padding: 24,
    borderWidth: 1,
    borderColor: '#bbf7d0',
    gap: 12,
  },
  resourcesTitle: {
    fontSize: 22,
    fontWeight: '700',
    color: '#166534',
  },
  resourcesText: {
    fontSize: 14,
    color: '#374151',
    lineHeight: 20,
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
