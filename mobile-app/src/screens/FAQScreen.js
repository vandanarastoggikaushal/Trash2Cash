import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
} from 'react-native';
import { MaterialCommunityIcons } from '@expo/vector-icons';
import { colors } from '../theme';

export default function FAQScreen() {
  const [expandedIndex, setExpandedIndex] = useState(null);

  const faqs = [
    {
      q: 'What items can Trash2Cash recycle in Wellington?',
      a: 'We collect rinsed aluminium cans, metal drink bottles, and appliances such as washing machines, dryers, dishwashers, microwaves, PC cases, laptops and other scrap metals.',
    },
    {
      q: 'How does the Wellington recycling pickup work?',
      a: 'Book a slot in the app, leave cans or appliances at your doorway, and our team will weigh, collect and update your Trash2Cash balance the same day.',
    },
    {
      q: 'Which suburbs do you service?',
      a: 'We cover the wider Wellington region including the CBD, Lower Hutt, Upper Hutt, Porirua, Tawa, Johnsonville, Karori, Newlands and Kapiti Coast suburbs.',
    },
    {
      q: 'Can my payment go to a child or KiwiSaver?',
      a: 'Yes. After your first pickup, add payout details in the account tab to send funds to a child’s bank account or your KiwiSaver provider.',
    },
    {
      q: 'Do cans need to be crushed or labelled?',
      a: 'No label is required—just give cans a quick rinse. Lightly crushing them can save space if you have a large volume.',
    },
    {
      q: 'How soon can you collect after I book?',
      a: 'Most recycling pickups happen within 2–4 business days. Larger community or business collections may be scheduled separately.',
    },
    {
      q: 'What condition should appliances be in?',
      a: 'Disconnect power and water, remove food residue, and ensure clear access. Our crew does the lifting and recycling.',
    },
    {
      q: 'Do you help schools or clubs with fundraising?',
      a: 'Absolutely. We support PTAs, sports clubs and events with can drives. Contact us to arrange a bulk collection.',
    },
    {
      q: 'What if I need to change or cancel a booking?',
      a: 'Let us know as soon as plans change—rescheduling is no problem and there are no cancellation fees.',
    },
    {
      q: 'How do I track payouts and balances?',
      a: 'Log in to your Trash2Cash account to see pending and completed payments, update bank details and download reference receipts.',
    },
    {
      q: 'Is there a pickup fee?',
      a: 'No collection fee. We simply deduct recycling costs from the value of your cans or appliances—you always see the final payout.',
    },
    {
      q: 'Do you provide documentation for businesses?',
      a: 'Yes. We supply digital reference IDs and summary statements suitable for sustainability or ESG reporting.',
    },
  ];

  const toggleFAQ = (index) => {
    setExpandedIndex(expandedIndex === index ? null : index);
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.section}>
        <Text style={styles.title}>❓ FAQ</Text>
        <Text style={styles.subtitle}>Everything you need to know</Text>

        {faqs.map((faq, index) => (
          <TouchableOpacity
            key={index}
            style={styles.faqCard}
            onPress={() => toggleFAQ(index)}
          >
            <View style={styles.faqHeader}>
              <View style={styles.faqNumber}>
                <Text style={styles.faqNumberText}>{index + 1}</Text>
              </View>
              <Text style={styles.faqQuestion}>{faq.q}</Text>
              <MaterialCommunityIcons
                name={expandedIndex === index ? 'chevron-up' : 'chevron-down'}
                size={24}
                color={colors.brand}
              />
            </View>
            {expandedIndex === index && (
              <View style={styles.faqAnswer}>
                <Text style={styles.faqAnswerText}>{faq.a}</Text>
              </View>
            )}
          </TouchableOpacity>
        ))}
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
  faqCard: {
    backgroundColor: '#fff',
    padding: 16,
    borderRadius: 12,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  faqHeader: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  faqNumber: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: colors.brand,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  faqNumberText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: 'bold',
  },
  faqQuestion: {
    flex: 1,
    fontSize: 16,
    fontWeight: 'bold',
    color: '#1f2937',
  },
  faqAnswer: {
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#e5e7eb',
    marginLeft: 44,
  },
  faqAnswerText: {
    fontSize: 14,
    color: '#374151',
    lineHeight: 20,
  },
});

