from flask import (Flask, render_template, url_for, request, redirect, flash)
from markupsafe import escape
import os
import json
import sys
processing_scripts_dir = os.path.dirname(os.path.dirname(os.path.realpath(__file__))) + '/facebook_inventory_processing/processing_scripts/'
sys.path.append(processing_scripts_dir)
interface_dir = os.path.dirname(os.path.dirname(os.path.realpath(__file__))) + '/facebook_inventory_processing/'
sys.path.append(interface_dir)
from logging_utils import initialize_ui_logging
from inventory_feed_processing_if import *
from inventory_feed_processing_constants import *

initialize_ui_logging()

def create_app(test_config=None):

    app = Flask(__name__, instance_relative_config=True)

    app.config['SECRET_KEY'] = 'test'


    # TODO: Replace with backend function call.
    def get_files():
        files = [
            '11554.csv',
            '13881.csv',
            '17857.csv',
            '17926.csv',
            '17928.csv',
            '18092.csv',
            '18652.csv',
            '18653.csv',
            '20573.csv',
            '21902.csv',
            '30747.csv',
            '3136.csv',
            '43597.csv',
            '51367.csv',
            '5242.csv',
            '9028875.csv',
            '9034900.csv',
            '9039367.csv',
            'ArizonaCarSalesWiki.csv',
            'CarFactoryDirect.csv',
            'MP10602.csv',
            'MP10969.csv',
            'MP12338.csv',
            'MP1252.csv',
            'MP1252FB.csv',
            'MP12777.csv',
            'MP13943.csv',
            'MP15955.csv',
            'MP15956.csv',
            'MP16728F.csv',
            'MP16728M.csv',
            'MP17611.csv',
            'MP18372.csv',
            'MP1907.csv',
            'MP19863.csv',
            'MP21041.csv',
            'MP277.csv',
            'MP3430.csv',
            'MP3536.csv',
            'MP4176FB.csv',
            'MP4679.csv',
            'MP5382.csv',
            'MP6505.csv',
            'MP8103.csv',
            'MP8204.csv',
            'MP8373.csv',
            'MP8457.csv',
            'MP9080.csv',
            'MP9146.csv',
            'VoglerMotorWiki.csv',
            'autofunds_carfactorydirect.csv',
            'colonialmotormart.csv',
            'colonialtoyota.csv',
            'crossroads_gm.csv',
            'fkcadillac_vehicles.csv',
            'fkchevy_vehicles.csv',
            'fkcountry_vehicles.csv',
            'gwinnettplacehonda.csv',
            'hondanewnan.csv',
            'inventory.csv',
            'townandcountryfeed.csv',
            'townandcountryfordlouisville.csv',
            'vehicles.csv',
            'voglerford.csv'
        ]

        return files


    def get_all_feeds():
        feeds = [{'name': 'Allentown Kia','slug': 'allentown-kia','domain': 'https://www.allentownkia.com/','inventory_source': ['9028875.csv', '9034900.csv'],'validate_urls': False},{'name': 'Arizona Cars','slug': 'arizona-cars','domain': 'https://www.arizona.cars/','inventory_source': ['ArizonaCarSalesWiki.csv'],'validate_urls': False},{'name': 'Car Factory Direct','slug': 'car-factory-direct','domain': 'https://www.carfactorydirect.com/','inventory_source': ['CarFactoryDirect.csv'],'validate_urls': False},{'name': 'Colonial Motor Mart','slug': 'colonial-motor-mart','domain': 'https://www.shopcolonialcars.com/','inventory_source': ['colonialmotormart.csv'],'validate_urls': False},{'name': 'Colonial Toyota','slug': 'colonial-toyota','domain': 'https://www.shopcolonialtoyota.com/','inventory_source': ['colonialtoyota.csv'],'validate_urls': False},{'name': 'Crossroads GM','slug': 'crossroads-gm','domain': 'https://www.crossroadsgm.net/','inventory_source': ['crossroads_gm.csv'],'validate_urls': False},{'name': 'Dan Cummings chevrolet buick paris','slug': 'dan-cummings-chevrolet-buick-paris','domain': 'https://www.dancummins.net/','inventory_source': ['MP12338.csv'],'validate_urls': False},{'name': 'Dan Cummings Chevy Buick Georgetown','slug': 'dan-cummings-chevy-buick-georgetown','domain': 'https://www.dancumminschevybuick.com/','inventory_source': ['MP1907.csv'],'validate_urls': False},{'name': 'Dan Cummins CDJR Georgetown','slug': 'dan-cummins-cdjr-georgetown','domain': 'https://www.dancumminscdjr.com/','inventory_source': ['MP1907.csv'],'validate_urls': False},{'name': 'Dan Cummins CDJR Paris','slug': 'dan-cummins-cdjr-paris','domain': 'https://www.dancumminschryslerdodgejeep.com/','inventory_source': ['MP10969.csv'],'validate_urls': False},{'name': 'Depaula Chevy','slug': 'depaula-chevy','domain': 'https://www.drivedepaula.com/','inventory_source': ['MP6505.csv'],'validate_urls': False},{'name': 'Depaula Ford','slug': 'depaula-ford','domain': 'https://www.depaulaford.net/','inventory_source': ['MP16728F.csv'],'validate_urls': False},{'name': 'Derrow CDJR','slug': 'derrow-cdjr','domain': 'https://www.drivederrow.com/','inventory_source': ['MP19863.csv'],'validate_urls': False},{'name': 'Duke Automotive','slug': 'duke-automotive','domain': 'https://www.dukeauto.com/','inventory_source': ['MP10602.csv'],'validate_urls': False},{'name': 'Faricy Boys','slug': 'faricy-boys','domain': 'https://www.faricy.com/','inventory_source': ['MP1252.csv'],'validate_urls': False},{'name': 'Findlay CDJR','slug': 'findlay-cdjr','domain': 'https://www.findlaychrysler.com/','inventory_source': ['MP8204.csv'],'validate_urls': False},{'name': 'FK ReAuto','slug': 'fk-reauto','domain': 'https://www.fkreauto.com/','inventory_source': ['MP8103.csv'],'validate_urls': False},{'name': 'Frank Kent Cadillac','slug': 'frank-kent-cadillac','domain': 'https://www.frankkentcadillac.com/','inventory_source': ['fkcadillac_vehicles.csv'],'validate_urls': False},{'name': 'Frank Kent CDJR','slug': 'frank-kent-cdjr','domain': 'https://www.frankkentcdjr.com/','inventory_source': ['MP8103.csv'],'validate_urls': False},{'name': 'Frank Kent CDJR','slug': 'frank-kent-cdjr','domain': 'https://www.frankkentcdjr.com/','inventory_source': ['MP18372.csv'],'validate_urls': False},{'name': 'Frank Kent CDJR','slug': 'frank-kent-cdjr','domain': 'https://www.frankkentcdjr.com/','inventory_source': ['MP13943.csv'],'validate_urls': False},{'name': 'Frank Kent Chevy','slug': 'frank-kent-chevy','domain': 'https://www.fkchevy.com/','inventory_source': ['fkchevy_vehicles.csv'],'validate_urls': False},{'name': 'Frank Kent Country','slug': 'frank-kent-country','domain': 'https://www.fkcountry.com/','inventory_source': ['fkcountry_vehicles.csv'],'validate_urls': False},{'name': 'Gwinnett Place Honda','slug': 'gwinnett-place-honda','domain': 'https://www.gphonda.com/','inventory_source': ['gwinnettplacehonda.csv'],'validate_urls': False},{'name': 'Hendrick Buick GMC Duluth GA','slug': 'hendrick-buick-gmc-duluth-ga','domain': 'https://www.rickhendrickbuickgmcatlanta.com/','inventory_source': ['20573.csv'],'validate_urls': False},{'name': 'Honda Of Newnan','slug': 'honda-of-newnan','domain': 'https://www.hondaofnewnan.com/','inventory_source': ['hondanewnan.csv'],'validate_urls': False},{'name': 'Keene CDJR','slug': 'keene-cdjr','domain': 'https://www.keenechryslerdodgejeep.com/','inventory_source': ['5242.csv'],'validate_urls': False},{'name': 'Kings Ford Inc','slug': 'kings-ford-inc','domain': 'https://www.kingsfordinc.com/','inventory_source': ['MP4679.csv'],'validate_urls': False},{'name': 'Newton Chevy Buick GMC','slug': 'newton-chevy-buick-gmc','domain': 'https://www.newtonchevroletbuickgmc.com/','inventory_source': ['MP14855.csv'],'validate_urls': False},{'name': 'Newton Ford South','slug': 'newton-ford-south','domain': 'https://www.newtonfordsouth.com/','inventory_source': ['MP14855.csv'],'validate_urls': False},{'name': 'Newton Nissan of Gallatin','slug': 'newton-nissan-of-gallatin','domain': 'https://www.newtonnissan.com/','inventory_source': ['MP15955.csv'],'validate_urls': False},{'name': 'Newton Nissan South','slug': 'newton-nissan-south','domain': 'https://www.newtonnissansouth.com/','inventory_source': ['MP15956.csv'],'validate_urls': False},{'name': 'Northeast Car Connection','slug': 'northeast-car-connection','domain': 'https://www.necarconnection.com','inventory_source': ['MP12777.csv'],'validate_urls': False},{'name': 'Parkway Kia','slug': 'parkway-kia','domain': 'https://www.parkwayfamilykia.com/','inventory_source': ['MP8457.csv'],'validate_urls': False},{'name': 'Parkway Mazda','slug': 'parkway-mazda','domain': 'https://www.parkwayfamilymazda.com/','inventory_source': ['MP8457.csv'],'validate_urls': False},{'name': 'Raceway Chevy','slug': 'raceway-chevy','domain': 'https://www.racewaychevy.com/','inventory_source': ['MP21041.csv'],'validate_urls': False},{'name': 'Raceway Kia','slug': 'raceway-kia','domain': 'https://www.racewaykia.com/','inventory_source': ['9039367.csv'],'validate_urls': False},{'name': 'Ralph Honda','slug': 'ralph-honda','domain': 'https://www.ralphhonda.com/','inventory_source': ['43597.csv'],'validate_urls': False},{'name': 'Romeoville Toyota','slug': 'romeoville-toyota','domain': 'https://www.romeovilletoyota.com/','inventory_source': ['MP9146.csv'],'validate_urls': False},{'name': 'Test FK Chevy','slug': 'test-fk-chevy','domain': 'https://www.fkchevy.com/','inventory_source': ['fkchevy_vehicles.csv'],'validate_urls': False},{'name': 'Thomas Nissan of Joliet','slug': 'thomas-nissan-of-joliet','domain': 'https://www.thomasnissanjoliet.com/','inventory_source': ['MP277.csv'],'validate_urls': False},{'name': 'Town and Country Ford','slug': 'town-and-country-ford','domain': 'https://www.fordlouisville.net/','inventory_source': ['townandcountryfeed.csv'],'validate_urls': False},{'name': 'Zimmer CDJR','slug': 'zimmer-cdjr','domain': 'https://www.zimmermotor.com/','inventory_source': ['MP5382.csv'],'validate_urls': False}]
        return feeds

    def get_client_logs():
        with open( 'sample_log/sample_log_return_json_format.json' ) as json_log:
            logs = json.load( json_log )
        return logs


    # TODO: Replace with backend function call.
    def get_csv_data():
        csv_data = [
            ['address.addr1', 'address.city', 'address.region', 'address.country', 'body_style', 'trim', 'description', 'url', 'drivetrain', 'exterior_color', 'image[0].url', 'image[0].tag[0]', 'fuel_type', 'sale_price', 'make', 'mileage.value', 'mileage.unit', 'model', 'price', 'transmission', 'state_of_vehicle', 'condition', 'vehicle_id', 'vin', 'year', 'title', 'availability', 'latitude', 'longitude', 'Dealer Postal Code', 'Dealer ID'],
            ['4950 New Car Dr', 'Colorado Springs', 'CO', 'US', 'SUV', '', "   **TRAILER TOW GROUP**, *AIR CONDITIONING*, *APPLE CARPLAY/GOOGLE ANDROID AUTO CAPABLE*, *BACK UP CAMERA*, *HARD TOP*, *HD ELECTRICAL GROUP*, *KEYLESS ENTRY*, *MEDIA HUB (USB, AUX)*, *POWER WINDOWS*, *SIRIUS XM*, *STEERING WHEEL MOUNTED AUDIO CONTROLS*, *TOUCH SCREEN DISPLAY*, *UNIVERSAL GARAGE DOOR OPENER*. $1,830 off MSRP!Thank you for choosing The Faricy Boys Chrysler Jeep, home of the Real Deal! The Real deal means we will match any Colorado dealer's advertised price on an in stock vehicle. (Excludes vehicles damaged in dealer inventory regardless of repair status. Prices quoted with manufacturer's rebate are subject to customer qualification for those rebates). We strive to be transparent in our new vehicle pricing so we only advertise rebates available to the general public. If you qualify for additional manufacturer's rebate we will gladly deduct those from our advertised price.Awards:  * ALG Residual Value AwardsReviews:  * Expected off-road strengths; new engine choices. Source: Edmunds", 'https://www.faricy.com/inventory/2018-jeep-wrangler-rubicon-4x4-sport-utility-1c4hjxcg6jw256613', '4WD', 'Punkn Metallic Clearcoat', 'http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-1.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-2.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-3.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-4.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-5.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-6.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-7.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-8.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-9.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-10.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-11.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-12.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-13.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-14.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-15.jpg|http://vehicle-photos-published.vauto.com/5a/47/f7/62-eccd-4108-b928-d5936d80cbb6/image-16.jpg', '', 'Gasoline', 'USD39995', 'Jeep', '', '', 'Wrangler', 'USD41825', 'Manual', 'new', 'Excellent', 'JW256613', '1C4HJXCG6JW256613', '2018', '2018 Jeep Wrangler Rubicon', 'AVAILABLE', '38.9385', '104.7362', '80923', ''],
            ['4950 New Car Dr', 'Colorado Springs', 'CO', 'US', 'SUV', '', "Price includes delivery and handling fee as well as the following rebates: $3,000 - 2019 Retail Consumer Cash **CK1. Exp. 10/01/2018   **8.4 INCH TOUCH SCREEN DISPLAY**, *9-SPEED AUTOMATIC TRANSMISSION*, *AIR CONDITIONING*, *APPLE CARPLAY / GOOGLE ANDROID AUTO CAPABLE*, *BACK UP CAMERA*, *BLIND SPOT CROSSPATH DETECTION*, *HEATED SEATS*, *HEATED STEERING WHEEL*, *KEYLESS START*, *POWER LIFTGATE*, *REMOTE START*, *SIRIUS XM*. $5,275 off MSRP!Thank you for choosing The Faricy Boys Chrysler Jeep, home of the Real Deal! The Real deal means we will match any Colorado dealer's advertised price on an in stock vehicle. (Excludes vehicles damaged in dealer inventory regardless of repair status. Prices quoted with manufacturer's rebate are subject to customer qualification for those rebates). We strive to be transparent in our new vehicle pricing so we only advertise rebates available to the general public. If you qualify for additional manufacturer's rebate we will gladly deduct those from our advertised price.", 'https://www.faricy.com/inventory/2019-jeep-cherokee-limited-4x4-sport-utility-1c4pjmdn9kd235453', '4WD', 'Diamond Black', 'http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-1.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-2.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-3.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-4.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-5.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-6.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-7.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-8.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-9.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-10.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-11.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-12.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-13.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-14.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-15.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-16.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-17.jpg|http://vehicle-photos-published.vauto.com/40/c7/4c/e0-2801-4d1d-a36a-61fd59098e41/image-18.jpg', '', 'Gasoline', 'USD31495', 'Jeep', '3', 'MI', 'Cherokee', 'USD36770', 'Manual', 'new', 'Excellent', 'KD235453', '1C4PJMDN9KD235453', '2019', '2019 Jeep Cherokee Limited', 'AVAILABLE', '38.9385', '104.7362', '80923', ''],
            ['4950 New Car Dr', 'Colorado Springs', 'CO', 'US', 'SUV', '', "Price includes delivery and handling fee as well as the following rebates: $2,000 - 2018 DE Retail Consumer Cash 74CJ1. Exp. 10/01/2018, $2,500 - 2018 Retail Bonus Cash DECJA1. Exp. 10/01/2018   $5,596 off MSRP!Thank you for choosing The Faricy Boys Chrysler Jeep, home of the Real Deal! The Real deal means we will match any Colorado dealer's advertised price on an in stock vehicle. (Excludes vehicles damaged in dealer inventory regardless of repair status. Prices quoted with manufacturer's rebate are subject to customer qualification for those rebates). We strive to be transparent in our new vehicle pricing so we only advertise rebates available to the general public. If you qualify for additional manufacturer's rebate we will gladly deduct those from our advertised price.", 'https://www.faricy.com/inventory/2018-jeep-renegade-trailhawk-4x4-sport-utility-zaccjbcb8jph28941', '4WD', 'Glacier Metallic', 'http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-1.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-2.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-3.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-4.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-5.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-6.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-7.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-8.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-9.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-10.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-11.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-12.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-13.jpg|http://vehicle-photos-published.vauto.com/f2/94/81/bf-ef68-4280-8c85-71511a85fd41/image-14.jpg', '', 'Gasoline', 'USD23439', 'Jeep', '7', 'MI', 'Renegade', 'USD29035', 'Manual', 'new', 'Excellent', 'JPH28941', 'ZACCJBCB8JPH28941', '2018', '2018 Jeep Renegade Trailhawk', 'AVAILABLE', '38.9385', '104.7362', '80923', ''],
            ['4950 New Car Dr', 'Colorado Springs', 'CO', 'US', 'SUV', '', "Price includes delivery and handling fee as well as the following rebates: $3,000 - 2019 Retail Consumer Cash **CK1. Exp. 10/01/2018, $500 - Denver 2019 Bonus Cash DECKA. Exp. 10/01/2018   $5,192 off MSRP!Thank you for choosing The Faricy Boys Chrysler Jeep, home of the Real Deal! The Real deal means we will match any Colorado dealer's advertised price on an in stock vehicle. (Excludes vehicles damaged in dealer inventory regardless of repair status. Prices quoted with manufacturer's rebate are subject to customer qualification for those rebates). We strive to be transparent in our new vehicle pricing so we only advertise rebates available to the general public. If you qualify for additional manufacturer's rebate we will gladly deduct those from our advertised price.", 'https://www.faricy.com/inventory/2019-jeep-cherokee-trailhawk-4x4-sport-utility-1c4pjmbn2kd182923', '4WD', 'Billet Silver Metallic', 'http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-1.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-2.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-3.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-4.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-5.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-6.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-7.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-8.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-9.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-10.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-11.jpg|http://vehicle-photos-published.vauto.com/a5/88/e1/b6-9d85-4d4c-87c7-b1291c7726bf/image-12.jpg', '', 'Gasoline', 'USD32318', 'Jeep', '3', 'MI', 'Cherokee', 'USD37510', 'Manual', 'new', 'Excellent', 'KD182923', '1C4PJMBN2KD182923', '2019', '2019 Jeep Cherokee Trailhawk', 'AVAILABLE', '38.9385', '104.7362', '80923', ''],
            ['4950 New Car Dr', 'Colorado Springs', 'CO', 'US', 'SUV', '', "   4WD. $8,120 off MSRP!Thank you for choosing The Faricy Boys Chrysler Jeep, home of the Real Deal! The Real deal means we will match any Colorado dealer's advertised price on an in stock vehicle. (Excludes vehicles damaged in dealer inventory regardless of repair status. Prices quoted with manufacturer's rebate are subject to customer qualification for those rebates). We strive to be transparent in our new vehicle pricing so we only advertise rebates available to the general public. If you qualify for additional manufacturer's rebate we will gladly deduct those from our advertised price.", '', '4WD', 'Ivory 3-Coat', 'http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-1.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-2.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-3.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-4.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-5.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-6.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-7.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-8.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-9.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-10.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-11.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-12.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-13.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-14.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-15.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-16.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-17.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-18.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-19.jpg|http://vehicle-photos-published.vauto.com/bb/89/65/89-8637-44e2-add1-4598ab38c2ae/image-20.jpg', '', 'Gasoline', 'USD91995', 'Jeep', '', '', 'Grand Cherokee', 'USD100115', 'Manual', 'new', 'Excellent', 'JC309060', 'AC4RJFN97JC309060', '2018', '2018 Jeep Grand Cherokee Trackhawk', 'AVAILABLE', '38.9385', '104.7362', '80923', '']
        ]
        return csv_data


    # Default landing page.
    @app.route('/')
    def home():
        return render_template( 'index.html' )


    # Displays active client inventory feeds from backend.
    # (Flask HTTP Request Handler)
    @app.route( '/active-feeds/' )
    def active_feeds():
        clients = get_active_feeds()
        logs = get_client_logs()
        return render_template( 'active-feeds.html', clients=clients, logs=logs )


    # Handles display/update/create client details form.
    # (Flask HTTP Request Handler)
    @app.route('/client-details/', defaults={'slug': None}, methods=['GET', 'POST'])
    @app.route('/client-details/<string:slug>/', methods=['GET', 'POST'] )
    def update_client_details(slug):
        # Process POST request.
        if request.method == 'POST':
            use_url_validation = False
            if 'validate_urls' in request.form:
                use_url_validation = True
            client_config_data = {
                CONFIG_CLIENT_NAME : request.form['name'],
                CONFIG_CLIENT_DOMAIN : request.form['domain'],
                CONFIG_INV_FEED_FILES : request.form.getlist('inventory_source'),
                CONFIG_USE_URL_VALIDATION : use_url_validation
            }

            errors = update_client_inventory_feed_configuration( client_config_data )
            if errors:
                flash(errors)
            else:
                return redirect( url_for( 'update_field_mapping', slug=client_config_data['client_id'] ) )

        # Process GET request.
        else:
            # TODO: Replace with call to backend interface.
            files = get_files()

            if slug:
                client_details = read_inventory_feed_configuration( slug )
            else:
                client_details = {
                    CONFIG_CLIENT_NAME: '',
                    CONFIG_CLIENT_ID: '',
                    CONFIG_INV_FEED_FILES: [],
                    CONFIG_CLIENT_DOMAIN: '',
                    CONFIG_USE_URL_VALIDATION: False
            }
            return render_template( 'client-details.html', slug=slug, client_details=client_details, files=files )


    # Handles display/update/create client field mapping form.
    # (Flask HTTP Request Handler)
    @app.route( '/field-mapping/<string:slug>/', methods=['GET', 'POST'] )
    def update_field_mapping(slug):
        if request.method == 'POST':
            # Format form request into list format accepted by back end interface.
            field_mapping_list = []
            for field in request.form:
                field_mapping_list.append( [ field,request.form[field] ] )

            # Save field mapping using back end interface.
            update_client_field_mapping( slug, field_mapping_list )
            return redirect(url_for('active_feeds'))

        # GET Request
        else:
            wikifields = get_wikim_standard_inventory_fields()
            client_details = read_inventory_feed_configuration( slug )
            csv_header = get_client_inventory_header( slug )
            current_field_mapping = read_client_field_mapping( slug )
            # Format current field mapping.
            current_field_mapping_dict = {}
            for row in current_field_mapping:
                current_field_mapping_dict[row[0]] = row[1]

            return render_template( 'field-mapping.html', current_field_mapping_dict=current_field_mapping_dict, slug=slug, wikifields=wikifields, client_details=client_details, csv_header=csv_header )

    # TODO: Replace with backend function call.
    def get_translation_rules():
        rules = [
            ['url', 'https://example.com/inventory/{{ vin }}'],
            ['title', '{{ year }} {{ make }} {{ model }} available now!']
        ]

        return rules

    # Handles display/update/create client translation rules form.
    # (Flask HTTP Request Handler)
    @app.route( '/translation-rules/<string:slug>/', methods=['GET', 'POST'] )
    def update_translations(slug):
        rules = get_translation_rules()

        fields = get_wikifields()

        feeds = get_all_feeds()
        client_details = {
            'name': '',
            'slug': '',
            'inventory_source': '',
            'domain': '',
            'validate_urls': False
        }
        for client in feeds:
            if ( client['slug'] == slug ):
                client_details = client

        return render_template( 'translation-rules.html', slug=slug, client_details=client_details, fields=fields, rules=rules )


    # Handler to run processing workflow for given client inventory feed.
    # TODO: Not implemented yet.
    @app.route( '/process-feed/<string:slug>/' )
    def run_process_feed(slug):
        return render_template( 'process-feed.html', slug=slug )


    # TODO: Replace with backend function call.
    def get_enum_fields():
        enum_fields = {
            "transmission" : {
                "Automatic" : ["a", "Variable", "CVT", "CVT w/OD"],
                "Manual" : ["m"]
            },
            "body_style" : {
                "CONVERTIBLE" : [],
                "COUPE" : ["CP", "2D Coupe"],
                "HATCHBACK" : ["HB", "4D Hatchback", "2D Hatchback"],
                "MINIVAN" : ["Mini-vansa, Passenger"],
                "TRUCK" : ["Regular Cab Chassis-Cab", "Crew Cab Chassis-Cab", "Regular Cab Chassis-Cab", "Regular Cab Pickup", "Crew Cab Pickup", "Extended Cab Pickup", "XLT 4x2 SuperCab Styleside 6.5 ft. box 145 in. WB","XL 4x4 SuperCab Styleside 6.5 ft. box 145 in. WB", "PU","XLT 4x4 SuperCrew Cab Styleside 5.5 ft. box 145 in. WB","XL 4x2 Regular Cab Styleside 6.5 ft. box 122 in. WB", "Extended Cab", "4D Double Cab", "4D Crew Cab", "4D SuperCrew", "2D Standard Cab"],
                "SUV" : ["Sport Utility", "Utility","UT","MP","4D Sport Utility"],
                "SEDAN" : ["4dr Car", "2dr Car", "SD", "4D Sedan", "2D Sedan"],
                "VAN" : ["VN"],
                "WAGON": ["WG","4D Wagon"],
                "CROSSOVER" : [],
                "SMALL_CAR" : [],
                "OTHER" : []
            },
            "drivetrain" : {
                "4X2" : [],
                "4X4" : ["4WD"],
                "AWD" : [],
                "FWD" : [],
                "RWD" : [],
                "Other" : []
            },
            "state_of_vehicle" : {
                "New" : ["N","0"],
                "Used" : ["U","1"],
                "CPO" : ["2"]
            },
            "fuel_type" : {
                "DIESEL" : [],
                "ELECTRIC" : [],
                "FLEX" : ["Fleixble Fuel"],
                "GASOLINE" : ["Gas"],
                "HYBRID" : [],
                "OTHER" : []
            },
            "condition" : {
                "EXCELLENT" : [],
                "GOOD" : [],
                "FAIR" : [],
                "POOR" : [],
                "OTHER" : []
            },
            "availability" : {
                "available" : [],
                "not available" : []
            }
        }

        return enum_fields


    # Handles display/update global facebook enumerable settings form.
    # (Flask HTTP Request Handler)
    @app.route( '/facebook-enumerable-settings/', methods=['GET', 'POST'] )
    def replacement_settings():

        enums = get_enum_fields()

        return render_template( 'facebook-enumerable-settings.html', enums=enums )

    @app.route( '/logs/', methods=['POST', 'GET'])
    def logs_view():
        log_data = get_client_logs()
        clients = get_all_feeds()
        return render_template( 'logs.html', clients=clients, log_data=log_data )

    # Return the app
    return app
