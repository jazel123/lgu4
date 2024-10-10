const leaveTypes = {
    'Vacation Leave': {
      description: 'Time off for rest, recreation, or personal business.',
      eligibility: 'All regular employees',
      days: 15
    },
    'Mandatory Leave': {
      description: 'Compulsory leave during specific periods.',
      eligibility: 'All employees',
      days: 5
    },
    'Sick Leave': {
      description: 'Time off due to illness or medical appointments.',
      eligibility: 'All employees',
      days: 15
    },
    'Maternity Leave': {
      description: 'Leave for female employees before and after childbirth.',
      eligibility: 'Female employees who are pregnant',
      days: 105
    },
    'Paternity Leave': {
      description: 'Leave for male employees to support their partner after childbirth.',
      eligibility: 'Male employees with a newborn child',
      days: 7
    },
    'Special Privilege Leave': {
      description: 'Leave for personal milestones, filial obligations, or parental duties.',
      eligibility: 'All regular employees',
      days: 3
    },
    'Solo Parent Leave': {
      description: 'Additional leave for single parents.',
      eligibility: 'Employees who are certified solo parents',
      days: 7
    },
    'Study Leave': {
      description: 'Leave for educational or professional development purposes.',
      eligibility: 'Employees pursuing relevant studies or training',
      days: 'Varies'
    },
    'VAWC Leave': {
      description: 'Leave for victims of violence against women and children.',
      eligibility: 'Employees who are victims of VAWC',
      days: 10
    },
    'Rehabilitation Leave': {
      description: 'Leave for employees who need to undergo rehabilitation for disabilities.',
      eligibility: 'Employees with disabilities requiring rehabilitation',
      days: 'Up to 6 months'
    },
    'Special Leave Benefits for Women': {
      description: 'Leave for women who undergo surgery due to gynecological disorders.',
      eligibility: 'Female employees undergoing specific medical procedures',
      days: 'Up to 2 months'
    },
    'Special Emergency (Calamity) Leave': {
      description: 'Leave during times of natural calamities or disasters.',
      eligibility: 'Employees affected by calamities',
      days: 5
    },
    'Monetization of Leave Credits': {
      description: 'Converting unused leave credits to cash.',
      eligibility: 'Employees with accumulated leave credits',
      days: 'Varies based on accumulated credits'
    },
    'Terminal Leave': {
      description: 'Leave credited to an employee upon retirement or separation from service.',
      eligibility: 'Retiring or separating employees',
      days: 'Based on accumulated leave credits'
    },
    'Adoption Leave': {
      description: 'Leave for employees who legally adopt a child.',
      eligibility: 'Employees who have legally adopted a child',
      days: 60
    }
  };