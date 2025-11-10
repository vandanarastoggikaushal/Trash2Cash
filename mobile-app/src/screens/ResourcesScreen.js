import React from 'react';
import { View, Text, ScrollView, StyleSheet, TouchableOpacity } from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { colors } from '../theme';

const resources = [
  {
    title: 'Wellington recycling guide: prepare cans & appliances',
    description:
      'Step-by-step advice on sorting aluminium cans, preparing appliances and maximising your Trash2Cash payout in Wellington.',
    icon: 'map-marker-radius',
    target: 'WellingtonRecycling',
    category: 'Household recycling',
  },
  {
    title: 'Seasonal recycling checklist (coming soon)',
    description:
      'Plan summer events, school drives and community clean-ups with a recycling checklist tailored for Wellington.',
    icon: 'calendar-star',
    target: null,
    category: 'Community ideas',
  },
  {
    title: 'Workspace & office recycling tips (coming soon)',
    description:
      'Keep aluminium cans and e-waste out of landfill with repeat pickups for cafés, gyms and office teams.',
    icon: 'office-building',
    target: null,
    category: 'Business recycling',
  },
];

export default function ResourcesScreen({ navigation }) {
  return (
    <ScrollView style={styles.container}>
      <View style={styles.hero}>
        <Text style={styles.heroTag}>📚 Recycling resources</Text>
        <Text style={styles.heroTitle}>Guides to recycle more &amp; earn more</Text>
        <Text style={styles.heroText}>
          Explore Trash2Cash tips for Wellington households, schools and businesses. Learn how to prepare cans, schedule pickups and
          grow savings with every collection.
        </Text>
      </View>

      <View style={styles.section}>
        {resources.map((resource) => (
          <TouchableOpacity
            key={resource.title}
            style={styles.card}
            activeOpacity={resource.target ? 0.8 : 1}
            onPress={() => resource.target && navigation.navigate(resource.target)}
          >
            <View style={styles.cardIcon}>
              <MaterialCommunityIcons name={resource.icon} size={28} color={colors.brand} />
            </View>
            <View style={styles.cardBody}>
              <Text style={styles.cardCategory}>{resource.category}</Text>
              <Text style={styles.cardTitle}>{resource.title}</Text>
              <Text style={styles.cardDescription}>{resource.description}</Text>
              {resource.target && (
                <View style={styles.cardLink}>
                  <Text style={styles.cardLinkText}>Read guide</Text>
                  <MaterialCommunityIcons name="chevron-right" size={18} color={colors.brand} />
                </View>
              )}
            </View>
          </TouchableOpacity>
        ))}
      </View>

      <View style={styles.cta}>
        <Text style={styles.ctaTitle}>Have a recycling question we should cover?</Text>
        <Text style={styles.ctaText}>
          We regularly add new resources. Suggest a tip for school fundraisers, apartment recycling or business pickups and we will add
          it to the hub.
        </Text>
        <TouchableOpacity
          style={styles.ctaButton}
          onPress={() => navigation.navigate('Main', { screen: 'Contact' })}
        >
          <Text style={styles.ctaButtonText}>Suggest a guide</Text>
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
  hero: {
    paddingHorizontal: 20,
    paddingTop: 32,
    paddingBottom: 12,
  },
  heroTag: {
    alignSelf: 'flex-start',
    backgroundColor: '#dcfce7',
    color: colors.brand,
    fontWeight: '600',
    fontSize: 12,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 999,
    marginBottom: 12,
  },
  heroTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 8,
  },
  heroText: {
    fontSize: 15,
    color: '#374151',
    lineHeight: 22,
  },
  section: {
    padding: 20,
    gap: 16,
  },
  card: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 18,
    borderWidth: 1,
    borderColor: '#bbf7d0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
    elevation: 3,
  },
  cardIcon: {
    marginRight: 16,
    marginTop: 6,
  },
  cardBody: {
    flex: 1,
    gap: 6,
  },
  cardCategory: {
    fontSize: 12,
    textTransform: 'uppercase',
    color: '#16a34a',
    fontWeight: '600',
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: '#1f2937',
  },
  cardDescription: {
    fontSize: 14,
    color: '#4b5563',
    lineHeight: 20,
  },
  cardLink: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginTop: 4,
  },
  cardLinkText: {
    fontSize: 14,
    fontWeight: '600',
    color: colors.brand,
  },
  cta: {
    margin: 20,
    padding: 24,
    borderRadius: 20,
    backgroundColor: '#15803d',
    gap: 12,
  },
  ctaTitle: {
    fontSize: 22,
    fontWeight: '700',
    color: '#fff',
  },
  ctaText: {
    fontSize: 14,
    color: '#d1fae5',
    lineHeight: 20,
  },
  ctaButton: {
    alignSelf: 'flex-start',
    backgroundColor: '#fff',
    paddingHorizontal: 18,
    paddingVertical: 12,
    borderRadius: 12,
  },
  ctaButtonText: {
    color: colors.brand,
    fontSize: 14,
    fontWeight: '700',
  },
});

