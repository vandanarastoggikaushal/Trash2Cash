import React from 'react';
import { ScrollView, View, Text, StyleSheet } from 'react-native';
import { colors } from '../theme';
import { APP_CONFIG } from '../config/api';

export default function WellingtonRecyclingScreen() {
  return (
    <ScrollView style={styles.container}>
      <View style={styles.hero}>
        <Text style={styles.heroTag}>📍 Wellington region</Text>
        <Text style={styles.heroTitle}>Recycling pickup for cans, appliances &amp; scrap metal</Text>
        <Text style={styles.heroText}>
          Trash2Cash NZ offers door-to-door recycling across Wellington City, Hutt Valley, Porirua and the Kapiti Coast.
          Leave rinsed aluminium cans or disconnected appliances at your doorway and we will handle the heavy lifting, recycling and payouts.
        </Text>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>What we collect</Text>
        <View style={styles.cardGrid}>
          <InfoCard title="Aluminium & metal cans" description="Rinsed drink cans, fundraising bags and food tins. Keep glass separate." />
          <InfoCard title="Household appliances" description="Washing machines, dryers, dishwashers, microwaves, laptops and more." />
          <InfoCard title="Community drives" description="Perfect for school can drives, club fundraisers and event recycling." />
          <InfoCard title="Business recycling runs" description="Regular pickups for cafés, offices, gyms and construction sites." />
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Suburbs we service</Text>
        <Text style={styles.sectionSubtitle}>
          We travel across the Wellington region each week. Don’t see your suburb? Contact us and we can usually add it to the schedule.
        </Text>
        <View style={styles.serviceGrid}>
          {APP_CONFIG.SERVICE_AREAS.map((area) => (
            <View key={area} style={styles.serviceCard}>
              <Text style={styles.serviceCardTitle}>{area}</Text>
              <Text style={styles.serviceCardText}>
                Trash collection and recycling pickup for cans, appliances and scrap metal in {area}.
              </Text>
            </View>
          ))}
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>How the recycling pickup works</Text>
        <View style={styles.stepList}>
          <Step
            number="1"
            title="Book online"
            description="Choose a date using the schedule form and let us know roughly what you’re recycling."
          />
          <Step
            number="2"
            title="Prepare your items"
            description="Rinse cans, disconnect appliances and leave them somewhere safe for our crew."
          />
          <Step
            number="3"
            title="We collect & weigh"
            description="Our team confirms volumes on-site, loads everything and updates your Trash2Cash balance."
          />
          <Step
            number="4"
            title="Choose your payout"
            description="Send earnings to a bank account, child savings or KiwiSaver once your first pickup is complete."
          />
        </View>
      </View>

      <View style={styles.section}>
        <Text style={styles.sectionTitle}>Community recycling ideas</Text>
        <Text style={styles.sectionSubtitle}>
          Recycling pickups make it easy to raise funds and cut waste. Try these Wellington success stories:
        </Text>
        <View style={styles.ideaList}>
          <Bullet text="Schools run month-long can drives and book a Trash2Cash pickup once cages are full." />
          <Bullet text="Sports clubs partner with local cafés to collect drink cans after weekend games." />
          <Bullet text="Businesses schedule quarterly appliance recycling as part of sustainability reporting." />
        </View>
      </View>

      <View style={styles.cta}>
        <Text style={styles.ctaTitle}>Ready to book a Wellington recycling pickup?</Text>
        <Text style={styles.ctaText}>
          Keep aluminium, appliances and scrap metal out of landfill and turn them into value. We can usually collect within a few days.
        </Text>
        <Text style={styles.ctaFooter}>Head back to the Schedule tab to lock in your pickup.</Text>
      </View>
    </ScrollView>
  );
}

function InfoCard({ title, description }) {
  return (
    <View style={styles.infoCard}>
      <Text style={styles.infoCardTitle}>{title}</Text>
      <Text style={styles.infoCardText}>{description}</Text>
    </View>
  );
}

function Step({ number, title, description }) {
  return (
    <View style={styles.step}>
      <View style={styles.stepNumber}>
        <Text style={styles.stepNumberText}>{number}</Text>
      </View>
      <View style={styles.stepBody}>
        <Text style={styles.stepTitle}>{title}</Text>
        <Text style={styles.stepDescription}>{description}</Text>
      </View>
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
    paddingTop: 36,
    paddingBottom: 12,
    backgroundColor: '#ecfdf5',
  },
  heroTag: {
    alignSelf: 'flex-start',
    backgroundColor: '#bbf7d0',
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
    marginBottom: 10,
  },
  heroText: {
    fontSize: 15,
    color: '#374151',
    lineHeight: 22,
  },
  section: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#1f2937',
    marginBottom: 12,
  },
  sectionSubtitle: {
    fontSize: 14,
    color: '#4b5563',
    lineHeight: 20,
    marginBottom: 16,
  },
  cardGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  infoCard: {
    width: '48%',
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 18,
    borderWidth: 1,
    borderColor: '#bbf7d0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.06,
    shadowRadius: 3,
    elevation: 2,
  },
  infoCardTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: '#1f2937',
    marginBottom: 6,
  },
  infoCardText: {
    fontSize: 13,
    color: '#4b5563',
    lineHeight: 18,
  },
  serviceGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  serviceCard: {
    width: '48%',
    backgroundColor: '#ffffff',
    borderRadius: 14,
    padding: 18,
    borderWidth: 1,
    borderColor: '#bbf7d0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 3,
    elevation: 2,
  },
  serviceCardTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: '#166534',
    marginBottom: 6,
  },
  serviceCardText: {
    fontSize: 13,
    color: '#374151',
    lineHeight: 18,
  },
  stepList: {
    gap: 12,
  },
  step: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    borderWidth: 1,
    borderColor: '#bbf7d0',
    alignItems: 'flex-start',
    gap: 14,
  },
  stepNumber: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: colors.brand,
    justifyContent: 'center',
    alignItems: 'center',
  },
  stepNumberText: {
    color: '#fff',
    fontWeight: '700',
    fontSize: 16,
  },
  stepBody: {
    flex: 1,
    gap: 4,
  },
  stepTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: '#1f2937',
  },
  stepDescription: {
    fontSize: 14,
    color: '#4b5563',
    lineHeight: 20,
  },
  ideaList: {
    gap: 12,
  },
  bulletItem: {
    flexDirection: 'row',
    gap: 10,
    alignItems: 'flex-start',
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
  cta: {
    margin: 20,
    padding: 24,
    borderRadius: 20,
    backgroundColor: '#15803d',
    gap: 10,
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
  ctaFooter: {
    fontSize: 13,
    color: '#bbf7d0',
    fontStyle: 'italic',
  },
});

