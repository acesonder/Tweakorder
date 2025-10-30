-- Case Templates Data
-- 50 templates for addiction, homelessness, and mental health case management

USE tweakorder;

-- Addiction Templates (15 templates)
INSERT INTO case_templates (name, category, description, template_content) VALUES
('Initial Substance Use Assessment', 'addiction', 'First assessment for substance use concerns', 'Client presents with concerns about substance use. Primary substance(s): [SUBSTANCE]\nFrequency of use: [FREQUENCY]\nLast use: [DATE]\nReadiness to change: [1-10 SCALE]\nSupport system: [DESCRIBE]\nNext steps: [ACTION ITEMS]'),

('Harm Reduction Plan', 'addiction', 'Creating a harm reduction strategy', 'Harm reduction goals discussed:\n- Safer use practices: [LIST]\n- Emergency contacts established: [YES/NO]\n- Naloxone kit provided: [YES/NO]\n- Safe consumption site information: [PROVIDED/NOT PROVIDED]\nFollow-up scheduled: [DATE]'),

('Recovery Support Check-in', 'addiction', 'Regular check-in for clients in recovery', 'Days clean/sober: [NUMBER]\nChallenges this week: [DESCRIBE]\nSupport meetings attended: [NUMBER]\nTriggers identified: [LIST]\nCoping strategies used: [DESCRIBE]\nNext appointment: [DATE]'),

('Detox Referral', 'addiction', 'Referral to detoxification services', 'Detox referral initiated for: [CLIENT NAME]\nFacility: [FACILITY NAME]\nContact: [PHONE/EMAIL]\nAppointment date: [DATE]\nTransportation arranged: [YES/NO]\nFollow-up support: [DESCRIBE]'),

('Relapse Prevention Plan', 'addiction', 'Developing relapse prevention strategies', 'Warning signs identified: [LIST]\nTrigger situations: [DESCRIBE]\nCoping mechanisms: [LIST]\nSupport contacts: [NAMES/NUMBERS]\nEmergency plan: [DESCRIBE]\nReview date: [DATE]'),

('Medication-Assisted Treatment (MAT)', 'addiction', 'MAT program enrollment and monitoring', 'MAT program: [SUBOXONE/METHADONE/OTHER]\nPrescribing physician: [NAME]\nDosage: [AMOUNT]\nPickup schedule: [FREQUENCY]\nSide effects: [DESCRIBE]\nCompliance: [GOOD/FAIR/POOR]'),

('Peer Support Connection', 'addiction', 'Connecting client with peer support', 'Peer support group: [NAME]\nMeeting schedule: [DAYS/TIMES]\nLocation: [ADDRESS]\nSponsor/mentor assigned: [YES/NO]\nFirst meeting attended: [DATE]\nClient feedback: [DESCRIBE]'),

('Family Counseling Referral', 'addiction', 'Referral for family therapy', 'Family members involved: [LIST]\nCounselor: [NAME]\nAgency: [NAME]\nFirst session: [DATE]\nGoals: [DESCRIBE]\nFollow-up: [DATE]'),

('Overdose Emergency Response', 'addiction', 'Documentation of overdose incident', 'Date/time of incident: [DATETIME]\nLocation: [ADDRESS]\nSubstance(s) involved: [LIST]\nNaloxone administered: [YES/NO BY WHOM]\nEMS called: [YES/NO]\nHospital transport: [YES/NO WHICH HOSPITAL]\nFollow-up care planned: [DESCRIBE]'),

('Substance Use Education Session', 'addiction', 'Educational intervention session', 'Topic covered: [TOPIC]\nMaterials provided: [LIST]\nClient understanding: [GOOD/FAIR/POOR]\nQuestions/concerns: [DESCRIBE]\nNext education session: [TOPIC/DATE]'),

('12-Step Program Introduction', 'addiction', '12-step program orientation', 'Program: [AA/NA/CA/OTHER]\nHome group identified: [YES/NO]\nSponsor search: [IN PROGRESS/COMPLETED]\nStep work begun: [STEP NUMBER]\nBig Book/literature provided: [YES/NO]\nNext milestone: [DESCRIBE]'),

('Dual Diagnosis Treatment', 'addiction', 'Co-occurring disorders treatment plan', 'Substance use disorder: [DESCRIBE]\nMental health diagnosis: [DESCRIBE]\nIntegrated treatment plan: [YES/NO]\nMedications: [LIST]\nTherapy schedule: [FREQUENCY]\nCoordination of care: [DESCRIBE]'),

('Sober Living Placement', 'addiction', 'Sober living home placement', 'Facility: [NAME]\nAddress: [ADDRESS]\nMove-in date: [DATE]\nHouse rules reviewed: [YES/NO]\nDeposit/rent: [AMOUNT]\nHouse manager: [NAME/CONTACT]\nDuration of stay: [TIMEFRAME]'),

('Employment Support - Recovery', 'addiction', 'Employment assistance for clients in recovery', 'Current employment status: [EMPLOYED/UNEMPLOYED]\nJob readiness: [ASSESS]\nDisclosure decision: [WILL/WON\'T DISCLOSE]\nSupported employment program: [YES/NO]\nJob search resources: [PROVIDED]\nNext steps: [ACTION ITEMS]'),

('Crisis Intervention - Substance Use', 'addiction', 'Crisis response for substance use emergency', 'Crisis type: [DESCRIBE]\nImmediate safety concerns: [YES/NO DESCRIBE]\nInterventions provided: [LIST]\nEmergency contacts notified: [YES/NO]\nReferrals made: [LIST]\nFollow-up within: [TIMEFRAME]'),

-- Homelessness Templates (15 templates)
('Housing Needs Assessment', 'homelessness', 'Initial assessment of housing needs', 'Current housing status: [SHELTERED/UNSHELTERED/COUCH SURFING]\nLength of homelessness: [DURATION]\nBarriers to housing: [LIST]\nIncome sources: [DESCRIBE]\nPreferences: [DESCRIBE]\nDocumentation status: [ID/BIRTH CERT/SSN]\nNext steps: [ACTION ITEMS]'),

('Emergency Shelter Placement', 'homelessness', 'Emergency shelter referral and placement', 'Shelter: [NAME]\nAddress: [ADDRESS]\nBed available: [YES/NO]\nCheck-in time: [TIME]\nLength of stay: [DURATION]\nRules/requirements: [DESCRIBE]\nCase manager: [NAME/CONTACT]'),

('Permanent Housing Application', 'homelessness', 'Application for permanent housing', 'Housing program: [NAME]\nApplication submitted: [DATE]\nDocuments needed: [LIST]\nInterview date: [DATE]\nIncome verification: [COMPLETED/PENDING]\nWaitlist position: [NUMBER]\nExpected move-in: [DATE]'),

('Rapid Re-housing Support', 'homelessness', 'Rapid re-housing program enrollment', 'Program: [NAME]\nRental assistance approved: [AMOUNT/MONTHS]\nUnit identified: [ADDRESS]\nLease signing: [DATE]\nMove-in assistance: [DESCRIBE]\nCase management frequency: [FREQUENCY]\nProgram duration: [MONTHS]'),

('Storage Assistance', 'homelessness', 'Help with storing belongings', 'Storage facility: [NAME]\nLocation: [ADDRESS]\nUnit size: [SIZE]\nCost: [AMOUNT/MONTH]\nPayment source: [AGENCY/CLIENT]\nAccess hours: [HOURS]\nDuration: [TIMEFRAME]'),

('Shower and Laundry Services', 'homelessness', 'Access to hygiene facilities', 'Facility: [NAME]\nAddress: [ADDRESS]\nServices used: [SHOWER/LAUNDRY/BOTH]\nHours available: [HOURS]\nFrequency: [TIMES PER WEEK]\nSupplies provided: [YES/NO]'),

('Mail and Phone Services', 'homelessness', 'Establishing communication access', 'Mailing address: [ADDRESS]\nPhone service: [PROVIDER]\nPhone number: [NUMBER]\nVoicemail setup: [YES/NO]\nEmail account: [YES/NO]\nComputer access location: [DESCRIBE]'),

('Benefits Application Assistance', 'homelessness', 'Help applying for benefits', 'Benefits applied for: [SNAP/TANF/SSI/SSDI/MEDICAID/OTHER]\nApplication date: [DATE]\nDocuments submitted: [LIST]\nInterview scheduled: [DATE]\nApproval status: [PENDING/APPROVED/DENIED]\nNext steps: [ACTION ITEMS]'),

('Medical Respite Care', 'homelessness', 'Medical respite placement', 'Medical condition: [DESCRIBE]\nRespite facility: [NAME]\nAdmission date: [DATE]\nExpected length of stay: [DAYS]\nMedical provider: [NAME]\nDischarge plan: [DESCRIBE]'),

('Veteran Housing Services', 'homelessness', 'VA housing assistance for veterans', 'Veteran status verified: [YES/NO]\nVA enrollment: [YES/NO]\nSVDP/HUD-VASH: [PROGRAM]\nDisability rating: [PERCENTAGE]\nVA case manager: [NAME/CONTACT]\nHousing voucher status: [PENDING/APPROVED]'),

('Winter Emergency Response', 'homelessness', 'Cold weather emergency services', 'Temperature: [DEGREES]\nEmergency shelter: [NAME/ADDRESS]\nWarming center hours: [HOURS]\nWinter supplies provided: [BLANKETS/COAT/GLOVES/BOOTS]\nTransportation to shelter: [YES/NO]\nWelfare check scheduled: [YES/NO]'),

('Document Recovery Assistance', 'homelessness', 'Help obtaining identification', 'Documents needed: [ID/BIRTH CERT/SSN CARD/OTHER]\nApplication submitted: [DATE]\nFees: [AMOUNT]\nFee assistance: [YES/NO SOURCE]\nExpected arrival: [DATE]\nPickup location: [ADDRESS]'),

('Encampment Outreach', 'homelessness', 'Outreach to unsheltered individuals', 'Location: [ADDRESS/DESCRIPTION]\nNumber of individuals: [NUMBER]\nServices offered: [LIST]\nSupplies distributed: [LIST]\nFollow-up needed: [YES/NO DESCRIBE]\nSafety concerns: [DESCRIBE]\nNext outreach: [DATE]'),

('Housing First Enrollment', 'homelessness', 'Housing First model enrollment', 'Program: [NAME]\nHousing placement: [ADDRESS]\nMove-in date: [DATE]\nLease in client name: [YES/NO]\nSupport services: [LIST]\nVisit frequency: [FREQUENCY]\nClient goals: [DESCRIBE]'),

('Family Shelter Services', 'homelessness', 'Family shelter placement and services', 'Family composition: [ADULTS/CHILDREN AGES]\nShelter: [NAME]\nAccommodation type: [PRIVATE ROOM/SHARED]\nSchool enrollment: [COMPLETED/PENDING]\nChildcare: [AVAILABLE/NEEDED]\nFamily services: [LIST]\nHousing search timeline: [DESCRIBE]'),

-- Mental Health Templates (20 templates)
('Mental Health Screening', 'mental_health', 'Initial mental health assessment', 'Presenting concerns: [DESCRIBE]\nSymptoms: [LIST]\nDuration: [TIMEFRAME]\nPrevious treatment: [YES/NO DESCRIBE]\nCurrent medications: [LIST]\nRisk assessment: [LOW/MEDIUM/HIGH]\nReferral needed: [YES/NO WHERE]'),

('Crisis Mental Health Intervention', 'mental_health', 'Mental health crisis response', 'Crisis type: [SUICIDAL/PSYCHOTIC/PANIC/OTHER]\nSafety assessment: [DESCRIBE]\nInterventions: [LIST]\nMobile crisis called: [YES/NO]\nHospitalization: [YES/NO VOLUNTARY/INVOLUNTARY]\nFollow-up plan: [DESCRIBE]'),

('Therapy Referral', 'mental_health', 'Referral to mental health counseling', 'Therapy type needed: [INDIVIDUAL/GROUP/FAMILY]\nTherapist/Agency: [NAME]\nContact: [PHONE/EMAIL]\nAppointment date: [DATE]\nInsurance verified: [YES/NO]\nTransportation arranged: [YES/NO]'),

('Psychiatric Medication Management', 'mental_health', 'Medication monitoring and support', 'Diagnosis: [DESCRIBE]\nMedications: [LIST WITH DOSAGES]\nPrescriber: [NAME]\nSide effects: [DESCRIBE]\nCompliance: [GOOD/FAIR/POOR]\nNext appointment: [DATE]'),

('Anxiety Management Plan', 'mental_health', 'Anxiety coping strategies', 'Anxiety triggers: [LIST]\nSymptoms experienced: [DESCRIBE]\nCoping strategies: [BREATHING/GROUNDING/OTHER]\nMedication: [YES/NO WHAT]\nTherapy: [TYPE/FREQUENCY]\nProgress: [DESCRIBE]'),

('Depression Support', 'mental_health', 'Depression intervention and support', 'Severity: [MILD/MODERATE/SEVERE]\nSymptoms: [LIST]\nDuration: [TIMEFRAME]\nSuicidal ideation: [YES/NO ACTIVE/PASSIVE]\nTreatment: [THERAPY/MEDS/BOTH]\nSupport system: [DESCRIBE]\nSafety plan: [YES/NO]'),

('PTSD Trauma-Informed Care', 'mental_health', 'Trauma-informed support plan', 'Trauma history: [GENERAL DESCRIPTION]\nTriggers identified: [LIST]\nTrauma therapy: [EMDR/CPT/PE/OTHER]\nSafety concerns: [DESCRIBE]\nCoping skills: [LIST]\nProgress: [DESCRIBE]'),

('Psychiatric Hospital Discharge', 'mental_health', 'Post-hospitalization follow-up', 'Hospital: [NAME]\nDischarge date: [DATE]\nDiagnosis: [DESCRIBE]\nMedications changed: [YES/NO DESCRIBE]\nOutpatient appointment: [DATE]\nCrisis plan: [DESCRIBE]\nSupport needed: [DESCRIBE]'),

('Wellness Recovery Action Plan (WRAP)', 'mental_health', 'WRAP development and review', 'Wellness tools: [LIST]\nDaily maintenance plan: [DESCRIBE]\nTriggers: [LIST]\nEarly warning signs: [LIST]\nWhen things are breaking down: [PLAN]\nCrisis plan: [CONTACTS/ACTIONS]\nPost-crisis plan: [DESCRIBE]'),

('Peer Support Mental Health', 'mental_health', 'Peer support connection', 'Peer support specialist: [NAME]\nGroup: [NAME/TYPE]\nMeeting schedule: [FREQUENCY]\nGoals: [DESCRIBE]\nProgress: [DESCRIBE]\nNext meeting: [DATE]'),

('Suicide Risk Assessment', 'mental_health', 'Suicide risk evaluation', 'Ideation: [YES/NO FREQUENCY]\nPlan: [YES/NO DESCRIBE]\nMeans: [ACCESS TO LETHAL MEANS]\nIntent: [LOW/MEDIUM/HIGH]\nProtective factors: [LIST]\nInterventions: [DESCRIBE]\nSafety plan created: [YES/NO]\nFollow-up: [WITHIN 24/48/72 HOURS]'),

('Psychoeducation Session', 'mental_health', 'Mental health education', 'Topic: [DIAGNOSIS/MEDICATION/COPING SKILLS/OTHER]\nMaterials provided: [LIST]\nClient understanding: [GOOD/FAIR/POOR]\nQuestions addressed: [DESCRIBE]\nNext session: [TOPIC/DATE]'),

('Cognitive Behavioral Therapy (CBT)', 'mental_health', 'CBT session documentation', 'Target thought: [DESCRIBE]\nCognitive distortion: [TYPE]\nEvidence for/against: [DESCRIBE]\nAlternative thought: [DESCRIBE]\nBehavioral experiment: [DESCRIBE]\nHomework assigned: [DESCRIBE]'),

('Supported Employment - Mental Health', 'mental_health', 'Employment support for mental health clients', 'Employment goal: [DESCRIBE]\nJob readiness: [ASSESS]\nAccommodations needed: [LIST]\nDisclosure decision: [DESCRIBE]\nSupported employment specialist: [NAME]\nJob search/placement: [STATUS]'),

('Family Psychoeducation', 'mental_health', 'Family education and support', 'Family members present: [LIST]\nDiagnosis explained: [YES/NO]\nTreatment plan reviewed: [YES/NO]\nFamily concerns: [DESCRIBE]\nSupport strategies: [DESCRIBE]\nNext family session: [DATE]'),

('Dialectical Behavior Therapy (DBT)', 'mental_health', 'DBT skills training', 'Module: [MINDFULNESS/DISTRESS TOLERANCE/EMOTION REG/INTERPERSONAL]\nSkill taught: [DESCRIBE]\nPractice exercise: [DESCRIBE]\nDiary card review: [COMPLETED/NOT COMPLETED]\nChain analysis: [IF APPLICABLE]\nHomework: [DESCRIBE]'),

('Assertive Community Treatment (ACT)', 'mental_health', 'ACT team visit', 'Team member: [NAME/ROLE]\nVisit location: [HOME/COMMUNITY]\nServices provided: [LIST]\nMedication administration: [YES/NO]\nCrisis intervention needed: [YES/NO]\nNext visit: [DATE/TIME]'),

('Social Skills Training', 'mental_health', 'Social skills development', 'Skill area: [CONVERSATION/ASSERTIVENESS/CONFLICT/OTHER]\nRole play scenario: [DESCRIBE]\nClient performance: [DESCRIBE]\nFeedback provided: [DESCRIBE]\nPractice assignment: [DESCRIBE]\nNext session: [DATE]'),

('Mental Health Court Coordination', 'mental_health', 'Mental health court compliance', 'Court date: [DATE]\nCompliance status: [COMPLIANT/NON-COMPLIANT]\nTreatment attendance: [PERCENTAGE]\nDrug testing: [RESULTS]\nJudge recommendation: [DESCRIBE]\nNext court date: [DATE]'),

('Recovery-Oriented Care Plan', 'mental_health', 'Person-centered recovery planning', 'Client strengths: [LIST]\nRecovery goals: [SHORT TERM/LONG TERM]\nBarriers: [DESCRIBE]\nSupports: [DESCRIBE]\nAction steps: [LIST]\nProgress indicators: [DESCRIBE]\nReview date: [DATE]');
