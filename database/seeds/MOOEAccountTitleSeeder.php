<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MooeClassification;
use App\Models\MooeAccountTitle;

class MOOEAccountTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $accountTitleData = [
            [
                'classification' => 'Traveling Expenses',
                'account_title' => 'Traveling Expenses-Local',
                'uacs_code' => '5020101000',
                'description' => 'This account is used to recognize the costs incurred in the movement/transport of government officers and employees within the country. This includes transportation, travel per diems, ferriage, and all other related expenses.',
            ], [
                'classification' => 'Traveling Expenses',
                'account_title' => 'Traveling Expenses-Foreign',
                'uacs_code' => '5020102000',
                'description' => 'This account is used to recognize the costs incurred in the movement/transport of government officers and employees within the country. This includes transportation, travel per diems, ferriage, and all other related expenses.',
            ], [
                'classification' => 'Training and Scholarship Expenses',
                'account_title' => 'Training Expenses',
                'uacs_code' => '5020201000',
                'description' => 'This account is used to recognize the costs incurred for the participation/attendance in and conduct of trainings, conventions and seminars/workshops. It includes training fees, honoraria of lecturers, cost of handouts, supplies, materials, meals, snacks and all other training related expenses.',
            ], [
                'classification' => 'Training and Scholarship Expenses',
                'account_title' => 'Scholarship Grants/Expenses ',
                'uacs_code' => '5020202000',
                'description' => 'This account is used to recognize the costs of scholarships granted by the government to individuals in the pursuit of further learning, study or research.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Office Supplies Expenses',
                'uacs_code' => '5020301000',
                'description' => 'This account is used to recognize the cost or value of office supplies such as bond paper, ink, and small tangible items like staple wire remover, puncher, stapler and other similar items issued to end-users for government operations.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Accountable Forms Expenses',
                'uacs_code' => '5020302000',
                'description' => 'This account is used to recognize the cost of accountable forms with or without money value such as official receipts, passports, tickets, permit/license plates, LTO plates, and the like, issued to end-users.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Non-Accountable Forms Expenses',
                'uacs_code' => '5020303000',
                'description' => 'This account is used to recognize the cost of non-accountable forms such as pre-printed application forms, tax returns forms, accounting forms and the like, issued to end-users.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Animal/Zoological Supplies Expenses',
                'uacs_code' => '5020304000',
                'description' => 'This account is used to recognize the costs of food, medicines, veterinary and other maintenance needs of animals issued for use in government parks, zoos, wildlife sanctuaries and botanical gardens. This also includes
                supplies issued for zoological researches, preservations, breeding and other purposes.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Food Supplies Expenses',
                'uacs_code' => '5020305000',
                'description' => 'This account is used to recognize the cost of food issued to hospital/rehabilitation patients, jail inmates and the like.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Welfare Goods Expenses',
                'uacs_code' => '5020306000',
                'description' => 'This account is used to recognize the cost of goods issued/distributed to people affected by calamities/disasters/ground conflicts such as canned goods, blankets, mats, kitchen utensils, flashlights and other similar items. This also includes the cost of food served to people affected by calamities/disasters/ground conflicts.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Drugs and Medicines Expenses',
                'uacs_code' => '5020307000',
                'description' => 'This account is used to recognize the costs of drugs and medicines issued to end-users for government operations.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Medical, Dental and Laboratory Supplies Expenses',
                'uacs_code' => '5020308000',
                'description' => 'This account is used to recognize the costs of medical, dental and laboratory supplies issued to end-users for government operations.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Fuel, Oil and Lubricants Expenses',
                'uacs_code' => '5020309000',
                'description' => 'This account is used to recognize the costs of fuel, oil and lubricants issued for use of government vehicles and other equipment in connection with government operations/projects.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Agricultural and Marine Supplies Expenses',
                'uacs_code' => '5020310000',
                'description' => 'This account is used to recognize the cost of fertilizers, pesticides and other marine and agricultural supplies issued in government operations/projects. This includes supplies issued for aquaculture researches, environment protections/ preservations and the like.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Textbooks and Instructional Materials Expenses',
                'uacs_code' => '5020311000',
                'description' => 'This account is used to recognize the cost of books and instructional materials distributed to public schools including flipcharts, video clips/slides, and the like.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Textbooks and Instructional Materials Expenses::Textbooks and Instructional Materials Expenses',
                'uacs_code' => '5020311001',
                'description' => 'This account is used to recognize the cost of books and instructional materials distributed to public schools including flipcharts, video clips/slides, and the like.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Textbooks and Instructional Materials Expenses::Chalk Allowance',
                'uacs_code' => '5020311002',
                'description' => 'This account is used to recognize the cost of books and instructional materials distributed to public schools including flipcharts, video clips/slides, and the like.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Military, Police and Traffic Supplies Expenses',
                'uacs_code' => '5020312000',
                'description' => 'This account is used to recognize the cost or value of military and police supplies issued/used in government operations such as clubs/cudgels, night sticks, police/traffic gears, flashlights, truncheons, ammunitions and the like.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Chemical and Filtering Supplies Expenses',
                'uacs_code' => '5020313000',
                'description' => 'This account is used to recognize the cost of chemical and filtering supplies used in government operations.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses',
                'uacs_code' => '5020321000',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Machinery',
                'uacs_code' => '5020321001',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Office Equipment',
                'uacs_code' => '5020321002',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Information and Communications Technology Equipment',
                'uacs_code' => '5020321003',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Agricultural and Forestry Equipment',
                'uacs_code' => '5020321004',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Marine and Fishery Equipment',
                'uacs_code' => '5020321005',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Airport Equipment',
                'uacs_code' => '5020321006',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Communications Equipment',
                'uacs_code' => '5020321007',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Disaster Response and Rescue Equipment',
                'uacs_code' => '5020321008',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Military, Police and Security Equipment',
                'uacs_code' => '5020321009',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Medical Equipment',
                'uacs_code' => '5020321010',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Printing Equipment',
                'uacs_code' => '5020321011',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Sports Equipment',
                'uacs_code' => '5020321012',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Technical and Scientific Equipment',
                'uacs_code' => '5020321013',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Machinery and Equipment Expenses::Other Machinery and Equipment',
                'uacs_code' => '5020321099',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Furniture, Fixtures and Books Expenses',
                'uacs_code' => '5020322000',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Furniture, Fixtures and Books Expenses::Furniture and Fixtures',
                'uacs_code' => '5020322001',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Semi-Expendable Furniture, Fixtures and Books Expenses::Books',
                'uacs_code' => '5020322002',
                'description' => 'This account is used to recognize the cost of semi-expendable machinery and equipment used in operation.',
            ], [
                'classification' => 'Supplies and Materials Expenses',
                'account_title' => 'Other Supplies and Materials Expenses',
                'uacs_code' => '5020399000',
                'description' => 'This account is used to recognize the cost of inventories issued to endusers not otherwise classified under the specific inventory expense accounts.',
            ], [
                'classification' => 'Utility Expenses',
                'account_title' => 'Water Expenses',
                'uacs_code' => '5020401000',
                'description' => 'This account is used to recognize the cost of water consumed in government operations/projects.',
            ], [
                'classification' => 'Utility Expenses',
                'account_title' => 'Electricity Expenses',
                'uacs_code' => '5020402000',
                'description' => 'This account is used to recognize the cost of electricity consumed in government operations/projects.',
            ], [
                'classification' => 'Utility Expenses',
                'account_title' => 'Gas/Heating Expenses',
                'uacs_code' => '5020403000',
                'description' => 'This account is used to recognize the gas/heating costs incurred by foreign-based government agencies during winter months.',
            ], [
                'classification' => 'Utility Expenses',
                'account_title' => 'Other Utility Expenses',
                'uacs_code' => '5020499000',
                'description' => 'This account is used to recognize the cost of utilities consumed in government operations/projects not falling in under any specific utility expense account.',
            ], [
                'classification' => 'Communication Expenses',
                'account_title' => 'Postage and Courier Expenses',
                'uacs_code' => '5020501000',
                'description' => 'This account is used to recognize the cost of delivery/transmission of official messages, mails, documents, recognizes and the like.',
            ], [
                'classification' => 'Communication Expenses',
                'account_title' => 'Telephone Expenses',
                'uacs_code' => '5020502000',
                'description' => 'This account is used to recognize the cost of transmitting messages thru telephone lines (mobile or landlines), faxes, telex and the like whether prepaid or postpaid.',
            ], [
                'classification' => 'Communication Expenses',
                'account_title' => 'Telephone Expenses::Mobile',
                'uacs_code' => '5020502001',
                'description' => 'This account is used to recognize the cost of transmitting messages thru telephone lines (mobile or landlines), faxes, telex and the like whether prepaid or postpaid.',
            ], [
                'classification' => 'Communication Expenses',
                'account_title' => 'Telephone Expenses::Landline',
                'uacs_code' => '5020502002',
                'description' => 'This account is used to recognize the cost of transmitting messages thru telephone lines (mobile or landlines), faxes, telex and the like whether prepaid or postpaid.',
            ], [
                'classification' => 'Communication Expenses',
                'account_title' => 'Internet Subscription Expenses',
                'uacs_code' => '5020503000',
                'description' => 'This account is used to recognize the cost of using internet services in government operations.',
            ], [
                'classification' => 'Communication Expenses',
                'account_title' => 'Cable, Satellite, Telegraph and Radio Expenses',
                'uacs_code' => '5020504000',
                'description' => 'This account is used to recognize the cost of using cable/satellite/telegram/radio services.',
            ], [
                'classification' => 'Awards/Rewards, Prizes and Indemnities',
                'account_title' => 'Awards/Rewards Expenses',
                'uacs_code' => '5020601000',
                'description' => 'This account is used to recognize the amount given in recognition of any civic or professional achievement, excellent performance and rewards to informers for the receipt of reliable information leading to the successful arrest/capture of fugitives, seizure/confiscation of smuggled goods, or collection of unpaid taxes/surcharges/fines/penalties. It also includes amount awarded by courts/administrative bodies to persons affected by the destruction of property/death/injury.',
            ], [
                'classification' => 'Awards/Rewards, Prizes and Indemnities',
                'account_title' => 'Awards/Rewards Expenses::Awards/Rewards Expenses',
                'uacs_code' => '5020601001',
                'description' => 'This account is used to recognize the amount given in recognition of any civic or professional achievement, excellent performance and rewards to informers for the receipt of reliable information leading to the successful arrest/capture of fugitives, seizure/confiscation of smuggled goods, or collection of unpaid taxes/surcharges/fines/penalties. It also includes amount awarded by courts/administrative bodies to persons affected by the destruction of property/death/injury.',
            ], [
                'classification' => 'Awards/Rewards, Prizes and Indemnities',
                'account_title' => 'Awards/Rewards Expenses::Rewards and Incentives',
                'uacs_code' => '5020601002',
                'description' => 'This account is used to recognize the amount given in recognition of any civic or professional achievement, excellent performance and rewards to informers for the receipt of reliable information leading to the successful arrest/capture of fugitives, seizure/confiscation of smuggled goods, or collection of unpaid taxes/surcharges/fines/penalties. It also includes amount awarded by courts/administrative bodies to persons affected by the destruction of property/death/injury.',
            ], [
                'classification' => 'Awards/Rewards, Prizes and Indemnities',
                'account_title' => 'Prizes',
                'uacs_code' => '5020602000',
                'description' => 'This account is used to recognize the amount paid to winners of competitive and promotional activities.',
            ], [
                'classification' => 'Awards/Rewards, Prizes and Indemnities',
                'account_title' => 'Indemnities',
                'uacs_code' => '5020603000',
                'description' => 'This account is used to recognize the amount awarded by courts/administrative bodies to persons affected by the destruction of property/death/injury.',
            ], [
                'classification' => 'Survey, Research, Exploration and Development Expenses',
                'account_title' => 'Survey Expenses',
                'uacs_code' => '5020701000',
                'description' => 'This account is used to recognize the cost incurred in the conduct of cadastral, structural, topographical, statistical and other type of surveys conducted by government agencies.',
            ], [
                'classification' => 'Survey, Research, Exploration and Development Expenses',
                'account_title' => 'Research, Exploration and Development Expenses',
                'uacs_code' => '5020702000',
                'description' => 'This account is used to recognize the cost incurred in the conduct of studies to gain scientific or technical knowledge on future projects including development, refinement or evaluation of policies for use of management.',
            ], [
                'classification' => 'Survey, Research, Exploration and Development Expenses',
                'account_title' => 'Demolition and Relocation Expenses',
                'uacs_code' => '5020801000',
                'description' => 'This account is used to recognize the costs of demolition of structures and relocation of settlers and structures affected by government projects.',
            ], [
                'classification' => 'Survey, Research, Exploration and Development Expenses',
                'account_title' => 'Desilting and Dredging Expenses',
                'uacs_code' => '5020802000',
                'description' => 'This account is used to recognize the costs incurred in removing large accumulation of decomposed litters and other organic debris in and deepening of canals, sewerage, rivers, creeks, and the like.',
            ], [
                'classification' => 'Generation, Transmission and Distribution Expenses',
                'account_title' => 'Generation, Transmission and Distribution Expenses',
                'uacs_code' => '5020901000',
                'description' => 'This account is used to recognize the costs of generation, transmission and distribution of water, electricity, information/communications, power and other related services intended for sale and/or redistribution.',
            ], [
                'classification' => 'Confidential, Intelligence and Extraordinary Expenses',
                'account_title' => 'Confidential Expenses',
                'uacs_code' => '5021001000',
                'description' => 'This account is used to recognize the amount paid for expenses related to surveillance activities in civilian government agencies that are intended to support the mandate or operations of the agency.',
            ], [
                'classification' => 'Confidential, Intelligence and Extraordinary Expenses',
                'account_title' => 'Intelligence Expenses',
                'uacs_code' => '5021002000',
                'description' => 'This account is used to recognize the amount paid for expenses related to intelligence information gathering activities of uniformed and military personnel, and intelligence practitioners that have direct impact to national
                security. The release of Intelligence Fund is subject to the approval of the President of the Philippines.',
            ], [
                'classification' => 'Confidential, Intelligence and Extraordinary Expenses',
                'account_title' => 'Extraordinary and Miscellaneous Expenses',
                'uacs_code' => '5021003000',
                'description' => 'This account is used to recognize the amount paid for expenses incidental to the performance of official functions, such as: meetings and conferences, public relations, educational, cultural and athletic activities,
                membership fees in government organizations, etc.',
            ], [
                'classification' => 'Professional Services',
                'account_title' => 'Legal Services',
                'uacs_code' => '5021101000',
                'description' => 'This account is used to recognize the cost incurred for authorized legal services rendered by private lawyers. This includes special counsel allowance granted to government lawyers deputized to represent the government in court as special counsel.',
            ], [
                'classification' => 'Professional Services',
                'account_title' => 'Auditing Services',
                'uacs_code' => '5021102000',
                'description' => 'This account is used to recognize the cost of operating expenses provided by auditees for auditing services rendered by the Commission on Audit.',
            ], [
                'classification' => 'Professional Services',
                'account_title' => 'Consultancy Services',
                'uacs_code' => '5021103000',
                'description' => 'This account is used to recognize the cost of services rendered by consultants contracted to perform particular outputs or services primarily advisory in nature and requiring highly specialized or technical expertise which cannot be provided by the regular staff of the agency.',
            ], [
                'classification' => 'Professional Services',
                'account_title' => 'Other Professional Services',
                'uacs_code' => '5021199000',
                'description' => 'This account is used to recognize the cost of other professional services contracted by the agency not otherwise classified under any of the specific professional services accounts.',
            ], [
                'classification' => 'General Services',
                'account_title' => 'Environment/Sanitary Services',
                'uacs_code' => '5021201000',
                'description' => 'This account is used to recognize the cost of services contracted for the upkeep and sanitation of the public places. This includes the cost of garbage and hospital waste collection and disposal.',
            ], [
                'classification' => 'General Services',
                'account_title' => 'Janitorial Services',
                'uacs_code' => '5021202000',
                'description' => 'This account is used to recognize the cost of janitorial services contracted by the government.',
            ], [
                'classification' => 'General Services',
                'account_title' => 'Security Services',
                'uacs_code' => '5021203000',
                'description' => 'This account is used to recognize the cost of security services contracted by the government.',
            ], [
                'classification' => 'General Services',
                'account_title' => 'Other General Services',
                'uacs_code' => '5021299000',
                'description' => 'This account is used to recognize the cost of other general services contracted by the agency not otherwise classified under any of the specific general services accounts.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Investment Property',
                'uacs_code' => '5021301000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on buildings/warehouses and other structures held for rent/lease or held for capital appreciation or both.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Land Improvements',
                'uacs_code' => '5021302000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on aquaculture structures and other land improvements constructed/ acquired/ developed for public use.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Land Improvements::Aquaculture Structures',
                'uacs_code' => '5021302001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on aquaculture structures and other land improvements constructed/ acquired/ developed for public use.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Land Improvements::Reforestation Projects',
                'uacs_code' => '5021302002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on aquaculture structures and other land improvements constructed/ acquired/ developed for public use.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Land Improvements::Other Land Improvements',
                'uacs_code' => '5021302099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on aquaculture structures and other land improvements constructed/ acquired/ developed for public use.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets',
                'uacs_code' => '5021303000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Road Networks',
                'uacs_code' => '5021303001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Flood Control Systems',
                'uacs_code' => '5021303002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Sewer Systems',
                'uacs_code' => '5021303003',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Water Supply Systems',
                'uacs_code' => '5021303004',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Power Supply Systems',
                'uacs_code' => '5021303005',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Communications Networks',
                'uacs_code' => '5021303006',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Seaport Systems',
                'uacs_code' => '5021303007',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Airport Systems',
                'uacs_code' => '5021303008',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Parks, Plazas and Monuments',
                'uacs_code' => '5021303009',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Railway Systems',
                'uacs_code' => '5021303010',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Infrastructure Assets::Other Infrastructure Assets',
                'uacs_code' => '5021303099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on road networks; flood control systems; sewer systems; water supply systems; power supply systems; communications networks; seaport systems; airport systems; parks, plazas, monuments; and other infrastructure assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures',
                'uacs_code' => '5021304000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures::Buildings',
                'uacs_code' => '5021304001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures::School Buildings',
                'uacs_code' => '5021304002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures::Hospitals and Health Centers',
                'uacs_code' => '5021304003',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures::Markets',
                'uacs_code' => '5021304004',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures::Slaughterhouses',
                'uacs_code' => '5021304005',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures::Hostels and Dormitories',
                'uacs_code' => '5021304006',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Buildings and Other Structures::Other Structures',
                'uacs_code' => '5021304099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on office buildings; school buildings; hospitals and health centers; markets; slaughterhouses; hostels and dormitories; and other structures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment',
                'uacs_code' => '5021305000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Machinery',
                'uacs_code' => '5021305001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Office Equipment',
                'uacs_code' => '5021305002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::ICT Equipment',
                'uacs_code' => '5021305003',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Agricultural and Forestry Equipment',
                'uacs_code' => '5021305004',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Marine and Fishery Equipment',
                'uacs_code' => '5021305005',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Airport Equipment',
                'uacs_code' => '5021305006',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Communication Equipment',
                'uacs_code' => '5021305007',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Construction and Heavy Equipment',
                'uacs_code' => '5021305008',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Disaster Response and Rescue Equipment',
                'uacs_code' => '5021305009',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Military, Police and Security Equipment',
                'uacs_code' => '5021305010',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Medical Equipment',
                'uacs_code' => '5021305011',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Printing Equipment',
                'uacs_code' => '5021305012',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Sports Equipment',
                'uacs_code' => '5021305013',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Technical and Scientific Equipment',
                'uacs_code' => '5021305014',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Machinery and Equipment::Other Machinery and Equipment',
                'uacs_code' => '5021305099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on machinery; office equipment; information and communications technology (ICT) equipment; agricultural and forestry equipment; marine and fishery equipment; airport equipment; communication equipment; construction and heavy equipment; disaster response and rescue equipment; military, police and security equipment; medical equipment; printing equipment; sports equipment; technical and scientific equipment; and other machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Transportation Equipment',
                'uacs_code' => '5021306000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on motor vehicles; trains; aircrafts; watercrafts; and other transportation equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Transportation Equipment::Motor Vehicles',
                'uacs_code' => '5021306001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on motor vehicles; trains; aircrafts; watercrafts; and other transportation equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Transportation Equipment::Trains',
                'uacs_code' => '5021306002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on motor vehicles; trains; aircrafts; watercrafts; and other transportation equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Transportation Equipment::Aircrafts and Aircrafts Ground Equipment',
                'uacs_code' => '5021306003',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on motor vehicles; trains; aircrafts; watercrafts; and other transportation equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Transportation Equipment::Watercrafts',
                'uacs_code' => '5021306004',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on motor vehicles; trains; aircrafts; watercrafts; and other transportation equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Transportation Equipment::Other Transportation Equipment',
                'uacs_code' => '5021306099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on motor vehicles; trains; aircrafts; watercrafts; and other transportation equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Furniture and Fixtures',
                'uacs_code' => '5021307000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance on furniture and fixtures.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets',
                'uacs_code' => '5021308000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of buildings; and other leased assets acquired by a lessee under a finance lease contract/agreement.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets::Buildings and Other Structures',
                'uacs_code' => '5021308001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of buildings; and other leased assets acquired by a lessee under a finance lease contract/agreement.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets::Machinery and Equipment',
                'uacs_code' => '5021308002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of buildings; and other leased assets acquired by a lessee under a finance lease contract/agreement.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets::Transportation Equipment',
                'uacs_code' => '5021308003',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of buildings; and other leased assets acquired by a lessee under a finance lease contract/agreement.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets::Other Leased Assets',
                'uacs_code' => '5021308099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of buildings; and other leased assets acquired by a lessee under a finance lease contract/agreement.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets Improvements',
                'uacs_code' => '5021309000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of improvements on land, buildings and other assets occupied by a lessee under an operating lease.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets Improvements::Land',
                'uacs_code' => '5021309001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of improvements on land, buildings and other assets occupied by a lessee under an operating lease.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets Improvements::Buildings',
                'uacs_code' => '5021309002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of improvements on land, buildings and other assets occupied by a lessee under an operating lease.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Leased Assets Improvements::Other Leased Assets Improvements',
                'uacs_code' => '5021309003',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of improvements on land, buildings and other assets occupied by a lessee under an operating lease.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Restoration and Maintenance-Heritage Assets',
                'uacs_code' => '5021310000',
                'description' => 'This account is used to recognize the cost of restoration and maintenance of heritage assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Restoration and Maintenance-Heritage Assets::Historical Buildings',
                'uacs_code' => '5021310001',
                'description' => 'This account is used to recognize the cost of restoration and maintenance of heritage assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Restoration and Maintenance-Heritage Assets::Works of Arts and Archeological Specimens',
                'uacs_code' => '5021310002',
                'description' => 'This account is used to recognize the cost of restoration and maintenance of heritage assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Restoration and Maintenance-Heritage Assets::Other Heritage Assets',
                'uacs_code' => '5021310003',
                'description' => 'This account is used to recognize the cost of restoration and maintenance of heritage assets.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment',
                'uacs_code' => '5021321000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Machinery',
                'uacs_code' => '5021321001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Office Equipment',
                'uacs_code' => '5021321002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Information and Communications Technology Equipment',
                'uacs_code' => '5021321003',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Agricultural and Forestry Equipment',
                'uacs_code' => '5021321004',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Marine and Fishery Equipment',
                'uacs_code' => '5021321005',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Airport Equipment',
                'uacs_code' => '5021321006',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Communication Equipment',
                'uacs_code' => '5021321007',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Disaster Response and Rescue Equipment',
                'uacs_code' => '5021321008',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Military, Police and Security Equipment',
                'uacs_code' => '5021321009',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Medical Equipment ',
                'uacs_code' => '5021321010',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Printing Equipment',
                'uacs_code' => '5021321011',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Sports Equipment',
                'uacs_code' => '5021321012',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Technical and Scientific Equipment',
                'uacs_code' => '5021321013',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Machinery and Equipment::Other Machinery and Equipment',
                'uacs_code' => '5021321099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable machinery and equipment.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Furniture, Fixtures and Books',
                'uacs_code' => '5021322000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable furniture, fixtures and books.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Furniture, Fixtures and Books::Furniture and Fixture',
                'uacs_code' => '5021322001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable furniture, fixtures and books.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Semi-Expendable Furniture, Fixtures and Books::Books',
                'uacs_code' => '5021322002',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of semi-expendable furniture, fixtures and books.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Other Property, Plant and Equipment',
                'uacs_code' => '5021399000',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of other property, plant and equipment not falling under any of the specific property, plant and equipment account.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Other Property, Plant and Equipment::Work/Zoo Animals',
                'uacs_code' => '5021399001',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of other property, plant and equipment not falling under any of the specific property, plant and equipment account.',
            ], [
                'classification' => 'Repairs and Maintenance',
                'account_title' => 'Repairs and Maintenance-Other Property, Plant and Equipment::Other Property, Plant and Equipment',
                'uacs_code' => '5021399099',
                'description' => 'This account is used to recognize the cost of repairs and maintenance of other property, plant and equipment not falling under any of the specific property, plant and equipment account.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Subsidy to NGAs',
                'uacs_code' => '5021401000',
                'description' => 'This account is used by the Bureau of the Treasury to recognize replenishments made to Authorized Government Servicing Banks for negotiated MDS checks and other payments on accounts of NGAs; constructive issuance of NCAA for advanced payments made by foreign creditors and donors; constructive issuance of CDC to Foreign Service Posts; Working Fund deposited to the foreign currency accounts of implementing NGAs for FAPs; constructive issuance of NCA for TRAs to BIR and Tax Expenditure Fund (TEF) Subsidy to GOCCs and NGAs.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to NGAs',
                'uacs_code' => '5021402000',
                'description' => 'This account is used by NGAs to recognize financial assistance to other NGAs through transfer of funds or assets.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units',
                'uacs_code' => '5021403000',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Tobacco Excise Tax (Virginia) per R.A. 7171',
                'uacs_code' => '5021403001',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Tobacco Excise Tax (Burley and Native) per R.A. 8240',
                'uacs_code' => '5021403002',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Mining Taxes per R.A. 7160',
                'uacs_code' => '5021403003',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Royalties per R.A. 7160',
                'uacs_code' => '5021403004',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Forestry Charges per R.A. 7160',
                'uacs_code' => '5021403005',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Fishery Charges per R.A. 7160',
                'uacs_code' => '5021403006',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Renewable Energy charges per R.A. 9513',
                'uacs_code' => '5021403007',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Income Tax Collections in ECO ZONES per R.A. 7922 and R.A. 8748',
                'uacs_code' => '5021403008',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Value Added Tax per R.A. 7643',
                'uacs_code' => '5021403009',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to Local Government Units::Value Added Tax in lieu of Franchise Tax per R.A. 7953 and R.A. 8407',
                'uacs_code' => '5021403010',
                'description' => 'This account is used to recognize financial assistance to LGUs through transfer of funds or assets for government programs/projects/activities.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations',
                'uacs_code' => '5021404000',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Subsidy Support to Operations of GOCCs',
                'uacs_code' => '5021404001',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Road Networks',
                'uacs_code' => '5021404002',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Flood Control Systems',
                'uacs_code' => '5021404003',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Sewer Systems',
                'uacs_code' => '5021404004',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Water Supply Systems',
                'uacs_code' => '5021404005',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Power Supply Systems',
                'uacs_code' => '5021404006',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Communications Networks',
                'uacs_code' => '5021404007',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Seaport Systems',
                'uacs_code' => '5021404008',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Airport Systems',
                'uacs_code' => '5021404009',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Parks, Plazas and Monuments',
                'uacs_code' => '5021404010',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Budgetary Support to Government-Owned or Controlled Corporations::Other Infrastructure Assets',
                'uacs_code' => '5021404099',
                'description' => 'This account is used to recognize the NG’s budgetary support to GOCCs/GFIs for operating expenses; conversion to subsidy of advances and interest on advances of the NG on GOCCs/GFIs’ loans; internal revenue taxes and customs duties; and tariffs on importation of the GOCCs chargeable against the tax expenditure subsidy.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Financial Assistance to NGOs/Pos',
                'uacs_code' => '5021405000',
                'description' => 'This account is used to recognize the financial assistance to NGOs/Pos through transfer of funds or assets for implementation of government programs/projects.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Internal Revenue Allotment',
                'uacs_code' => '5021406000',
                'description' => 'This account is used to recognize the amount of internal revenue allotment (IRA) due to LGUs.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Subsidy to Regional Offices/Staff Bureaus',
                'uacs_code' => '5021407000',
                'description' => 'This account is used to recognize the amount of funds/assets transferred by the Central Office to the Regional Offices/Staff Bureaus of an agency or department.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Subsidy to Operating Units',
                'uacs_code' => '5021408000',
                'description' => 'This account is used to recognize the amount of funds/assets transferred by the Central Office/Regional Offices/Staff Bureaus to the Operating Units of an agency or department.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Subsidy to Other Funds',
                'uacs_code' => '5021409000',
                'description' => 'This account is used to recognize the amount of funds/assets transferred to other funds.',
            ], [
                'classification' => 'Financial Assistance/Subsidy',
                'account_title' => 'Subsidies-Others',
                'uacs_code' => '5021499000',
                'description' => 'This account is used to recognize the financial assistance given to individuals and institutions other than government agencies or NGOs/Pos for government programs/projects/activities.',
            ], [
                'classification' => 'Taxes, Insurance Premiums and Other Fees',
                'account_title' => 'Taxes, Duties and Licenses',
                'uacs_code' => '5021501000',
                'description' => 'This account is used to recognize the amount of taxes, duties, licenses and other fees due to regulatory agencies. This also includes taxes on interest income on savings deposits, time deposits, and government securities of the bond sinking fund/other funds.',
            ], [
                'classification' => 'Taxes, Insurance Premiums and Other Fees',
                'account_title' => 'Taxes, Duties and Licenses::Taxes, Duties and Licenses',
                'uacs_code' => '5021501001',
                'description' => 'This account is used to recognize the amount of taxes, duties, licenses and other fees due to regulatory agencies. This also includes taxes on interest income on savings deposits, time deposits, and government securities of the bond sinking fund/other funds.',
            ], [
                'classification' => 'Taxes, Insurance Premiums and Other Fees',
                'account_title' => 'Taxes, Duties and Licenses::Tax Refund',
                'uacs_code' => '5021501002',
                'description' => 'This account is used to recognize the amount of taxes, duties, licenses and other fees due to regulatory agencies. This also includes taxes on interest income on savings deposits, time deposits, and government securities of the bond sinking fund/other funds.',
            ], [
                'classification' => 'Taxes, Insurance Premiums and Other Fees',
                'account_title' => 'Fidelity Bond Premiums',
                'uacs_code' => '5021502000',
                'description' => 'This account is used to recognize the amount of premiums paid by the agency for the fidelity bonds of accountable officers.',
            ], [
                'classification' => 'Taxes, Insurance Premiums and Other Fees',
                'account_title' => 'Insurance Expenses',
                'uacs_code' => '5021503000',
                'description' => 'This account is used to recognize the amount of premiums paid by the agency for the insurable risks of government properties.',
            ], [
                'classification' => 'Labor and Wages',
                'account_title' => 'Labor and Wages',
                'uacs_code' => '5021601000',
                'description' => 'This account is used to recognize the costs incurred for labor and wages. These include labor payroll paid for projects undertaken by administration, for agricultural activities involving hired labor, student wages, etc.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Advertising Expenses',
                'uacs_code' => '5029901000',
                'description' => 'This account is used to recognize the costs incurred for advertisement, such as expenses to (a) promote and market products and services; and (b) publish invitations to bid and other authorized government advertisements.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Printing and Publication Expenses',
                'uacs_code' => '5029902000',
                'description' => 'This account is used to recognize the costs of printing and binding of manuscripts/documents, forms, manuals, brochures, pamphlets, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Representation Expenses',
                'uacs_code' => '5029903000',
                'description' => 'This account is used to recognize the expenses incurred for official meetings/conferences and other official functions.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Transportation and Delivery Expenses',
                'uacs_code' => '5029904000',
                'description' => 'This account is used to recognize the cost of transporting goods/merchandise sold in the course of business operations. This includes the cost of moving agency’s own people and properties from one station to another.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses',
                'uacs_code' => '5029905000',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses::Rents-Buildings and Structures',
                'uacs_code' => '5029905001',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses::Rents-Land',
                'uacs_code' => '5029905002',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses::Rents-Motor Vehicles',
                'uacs_code' => '5029905003',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses::Rents-Equipment',
                'uacs_code' => '5029905004',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses::Rents-Living Quarters',
                'uacs_code' => '5029905005',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses::Operating Lease',
                'uacs_code' => '5029905006',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Rent/Lease Expenses::Financial Lease',
                'uacs_code' => '5029905007',
                'description' => 'This account is used to recognize rental/lease of land, buildings, facilities, equipment, vehicles, machineries, and the like.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Membership Dues and Contributions to Organizations',
                'uacs_code' => '5029906000',
                'description' => 'This account is used to recognize membership fees/dues/contributions to recognized/authorized professional organizations.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Subscription Expenses',
                'uacs_code' => '5029907000',
                'description' => 'This account is used to recognize the cost of subscriptions to library and other reading materials.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Donations',
                'uacs_code' => '5029908000',
                'description' => 'This account is used to recognize the amount of donations to other levels of government and individuals and institutions.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Litigation/Acquired Assets Expenses',
                'uacs_code' => '5029909000',
                'description' => 'This account is used to recognize expenses incurred in connection with litigation proceedings and registration/consolidation of ownership of acquired assets, as well as those incurred in their preservation/maintenance.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Loss on Guaranty',
                'uacs_code' => '5029910000',
                'description' => 'This account is used to recognize the losses incurred for loans/indebtedness guaranteed by the government as authorized by law or competent authority. This also includes claims for foreign exchange risk cover and credit risk
                cover on foreign loans of GFIs; debt service payments on projects under the Build-Operate-Transfer (BOT) scheme or its variants assumed by the NG; and amount paid by the NG for guaranteed GOCC loans due to insolvency.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Other Maintenance and Operating Expenses',
                'uacs_code' => '5029999000',
                'description' => 'This account is used to recognize other operating expenses not falling under any of the specific maintenance and other operating expense accounts.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Other Maintenance and Operating Expenses::Website Maintenance',
                'uacs_code' => '5029999001',
                'description' => 'This account is used to recognize other operating expenses not falling under any of the specific maintenance and other operating expense accounts.',
            ], [
                'classification' => 'Other Maintenance and Operating Expenses',
                'account_title' => 'Other Maintenance and Operating Expenses::Other Maintenance and Operating Expensess',
                'uacs_code' => '5029999002',
                'description' => 'This account is used to recognize other operating expenses not falling under any of the specific maintenance and other operating expense accounts.',
            ]
        ];

        foreach ($accountTitleData as $orderNo => $data) {
            try {
                $classification = $data['classification'];
                $mooeClassData = DB::table('mooe_classifications')
                                ->where('classification_name', $classification)
                                ->first();
                $classificationID = $mooeClassData->id;
                $accountTitle = $data['account_title'];
                $uacsCode = $data['uacs_code'];
                $description = $data['description'];

                $instanceAccountTitle = new MooeAccountTitle;
                $instanceAccountTitle->classification_id = $classificationID;
                $instanceAccountTitle->account_title = $accountTitle;
                $instanceAccountTitle->uacs_code = $uacsCode;
                $instanceAccountTitle->description = $description;
                $instanceAccountTitle->order_no = $orderNo + 1;
                $instanceAccountTitle->save();

                echo "MOOE Account Title '$accountTitle' successfully created.\n";
            } catch (\Throwable $th) {
                echo "There is an error in seeding MOOE Account Title.\n";
            }
        }
    }
}
