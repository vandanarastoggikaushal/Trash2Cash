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
      q: 'What do you collect?',
      a: 'Clean aluminium cans and common household metal appliances.',
    },
    {
      q: 'How do payments work?',
      a: 'We tally your items, then pay out or transfer as chosen.',
    },
    {
      q: 'Kids & KiwiSaver?',
      a: 'Name a child beneficiary or provide KiwiSaver provider/member ID; we transfer after verification.',
    },
    {
      q: 'Do I need to crush cans?',
      a: 'Optional; please give a quick rinse.',
    },
    {
      q: 'Which suburbs?',
      a: 'Current service areas across Wellington region; more coming soon.',
    },
    {
      q: 'Appliance condition?',
      a: 'Must be safe to move; we handle recycling.',
    },
    {
      q: 'Turnaround time?',
      a: 'Usually a few days depending on suburb and volume.',
    },
    {
      q: 'Cancelled pickups?',
      a: 'Let us know ASAP - no worries, we will reschedule.',
    },
    {
      q: 'Hygiene?',
      a: 'Please rinse cans to keep collections clean and safe.',
    },
    {
      q: 'Heavy items?',
      a: 'We handle the heavy lifting—just ensure clear access.',
    },
    {
      q: 'Data privacy?',
      a: 'We store minimal details securely and never sell your data.',
    },
    {
      q: 'Receipts?',
      a: 'You will receive a reference ID after scheduling and confirmation after pickup.',
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

