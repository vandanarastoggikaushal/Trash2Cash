import React from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
} from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { APP_CONFIG } from '../config/api';
import { colors } from '../theme';

export default function HowItWorksScreen() {
  const steps = [
    {
      icon: 'account-plus',
      title: 'Register',
      description: 'Quick signup',
      number: 1,
    },
    {
      icon: 'broom',
      title: 'Prepare recyclables',
      description: 'Rinse & organize',
      number: 2,
    },
    {
      icon: 'calendar-clock',
      title: 'Schedule pickup',
      description: 'Book online',
      number: 3,
    },
    {
      icon: 'cash-multiple',
      title: 'Get paid / KiwiSaver',
      description: 'Earn money',
      number: 4,
    },
  ];

  return (
    <ScrollView style={styles.container}>
      <View style={styles.section}>
        <Text style={styles.title}>How It Works</Text>
        <Text style={styles.subtitle}>
          Simple steps to turn your recyclables into cash or savings
        </Text>

        {/* Steps */}
        <View style={styles.stepsContainer}>
          {steps.map((step, index) => (
            <View key={index} style={styles.stepCard}>
              <View style={styles.stepNumber}>
                <Text style={styles.stepNumberText}>{step.number}</Text>
              </View>
              <MaterialCommunityIcons
                name={step.icon}
                size={48}
                color={colors.brand}
                style={styles.stepIcon}
              />
              <Text style={styles.stepTitle}>{step.title}</Text>
              <Text style={styles.stepDescription}>{step.description}</Text>
            </View>
          ))}
        </View>

        {/* Tips */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>💡 Preparation Tips</Text>
          <TipItem text="Rinse cans quickly (crushing optional)" />
          <TipItem text="Keep appliances safe to move" />
          <TipItem text="Typical turnaround is a few days depending on suburb" />
        </View>

        {/* Service Areas */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>📍 Service Areas</Text>
          <Text style={styles.cardText}>We currently serve:</Text>
          <View style={styles.areasContainer}>
            {APP_CONFIG.SERVICE_AREAS.map((area, index) => (
              <View key={index} style={styles.areaBadge}>
                <Text style={styles.areaText}>{area}</Text>
              </View>
            ))}
          </View>
        </View>
      </View>
    </ScrollView>
  );
}

function TipItem({ text }) {
  return (
    <View style={styles.tipItem}>
      <MaterialCommunityIcons name="check-circle" size={20} color={colors.brand} />
      <Text style={styles.tipText}>{text}</Text>
    </View>
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
  stepsContainer: {
    marginBottom: 24,
  },
  stepCard: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    marginBottom: 16,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  stepNumber: {
    position: 'absolute',
    top: 12,
    right: 12,
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: colors.brand,
    justifyContent: 'center',
    alignItems: 'center',
  },
  stepNumberText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: 'bold',
  },
  stepIcon: {
    marginTop: 20,
    marginBottom: 12,
  },
  stepTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 4,
  },
  stepDescription: {
    fontSize: 14,
    color: '#6b7280',
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
  cardText: {
    fontSize: 14,
    color: '#6b7280',
    marginBottom: 12,
  },
  tipItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 8,
  },
  tipText: {
    fontSize: 14,
    color: '#374151',
    marginLeft: 12,
    flex: 1,
  },
  areasContainer: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  areaBadge: {
    backgroundColor: '#ecfdf5',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: colors.brand,
  },
  areaText: {
    fontSize: 12,
    color: colors.brand,
    fontWeight: '500',
  },
});

