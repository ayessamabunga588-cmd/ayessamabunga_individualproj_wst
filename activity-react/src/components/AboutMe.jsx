import React from 'react';

// Edit these values with your own details
const profile = {
  name: 'Your Name',
  title: 'Full-Stack Developer',
  location: 'Your City, Country',
  email: 'you@example.com',
  bio: 'Write a short introduction about yourself, your studies, and the kind of projects you enjoy building.',
  skills: ['Laravel', 'React', 'MySQL', 'REST APIs', 'Laravel Sanctum'],
};

export default function AboutMe() {
  return (
    <div style={styles.container}>
      <div style={styles.card}>
        <div style={styles.avatar}>{profile.name.charAt(0)}</div>
        <h1 style={styles.name}>{profile.name}</h1>
        <p style={styles.title}>{profile.title}</p>
        <p style={styles.meta}>{profile.location} · {profile.email}</p>
        <p style={styles.bio}>{profile.bio}</p>
        <h3 style={styles.subheading}>Skills</h3>
        <div style={styles.skillRow}>
          {profile.skills.map((skill) => (
            <span key={skill} style={styles.skill}>{skill}</span>
          ))}
        </div>
      </div>
    </div>
  );
}

const styles = {
  container: { maxWidth: '700px', margin: '40px auto', padding: '0 20px' },
  card: { backgroundColor: '#fff', border: '1px solid #e5e7eb', borderRadius: '12px', padding: '32px', textAlign: 'center' },
  avatar: { width: '80px', height: '80px', borderRadius: '50%', backgroundColor: '#2563eb', color: '#fff', fontSize: '32px', fontWeight: '700', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px' },
  name: { fontSize: '28px', margin: '0 0 4px', color: '#111827' },
  title: { color: '#2563eb', fontWeight: '600', margin: '0 0 4px' },
  meta: { color: '#6b7280', fontSize: '14px', margin: '0 0 20px' },
  bio: { color: '#4b5563', lineHeight: '1.6', textAlign: 'left' },
  subheading: { textAlign: 'left', marginTop: '24px', color: '#111827' },
  skillRow: { display: 'flex', flexWrap: 'wrap', gap: '8px' },
  skill: { backgroundColor: '#eff6ff', color: '#1d4ed8', padding: '6px 12px', borderRadius: '999px', fontSize: '13px', fontWeight: '500' },
};
